<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Notifications\CredentialsGenerated;
use App\Notifications\SubscriptionPending;
use App\Services\CurrencyService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subscriptions = Subscription::where('agent_id', $request->user()->id)
            ->with('client')
            ->latest()
            ->paginate(15);

        return view('agent.subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $subscriptionTypes = SubscriptionType::where('is_active', true)->orderBy('display_order')->get();
        return view('agent.subscriptions.create', compact('subscriptionTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_phone' => ['required', 'string', 'max:50'],
            'client_email' => ['required', 'email'],
            // Accept either a subscription_type_id or a simple type slug like 'weekly'|'monthly'
            'subscription_type_id' => ['nullable', 'exists:subscription_types,id'],
            'type' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'currency' => ['required', 'in:usd,fc'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        // Resolve subscription type: prefer explicit id, fallback to slug or default mapping
        $subscriptionType = null;
        $totalDays = 0;

        if (!empty($data['subscription_type_id'])) {
            $subscriptionType = SubscriptionType::find($data['subscription_type_id']);
        } elseif (!empty($data['type'])) {
            $subscriptionType = SubscriptionType::where('slug', $data['type'])->first();
        }

        if ($subscriptionType) {
            $totalDays = $subscriptionType->duration_days;
        } else {
            $map = ['weekly' => 5, 'monthly' => 20];
            $totalDays = $map[$data['type'] ?? 'monthly'] ?? 20;
        }
        $existingClient = User::where('email', $data['client_email'])->first();
        if (! $existingClient && User::where('phone', $data['client_phone'])->exists()) {
            return redirect()->route('agent.subscriptions.index')
                ->with('error', 'Un utilisateur avec ce numéro de téléphone existe déjà. Utilisez un autre numéro.');
        }

        $price = (float) $data['price'];
        $currencyService = app(CurrencyService::class);
        $priceFc = $data['currency'] === 'fc' ? $price : $currencyService->usdToFc($price);
        $priceUsd = $data['currency'] === 'usd' ? $price : $currencyService->fcToUsd($price);

        $subscription = Subscription::create([
            'agent_id' => $request->user()->id,
            'client_name' => $data['client_name'],
            'client_phone' => $data['client_phone'],
            'client_email' => $data['client_email'],
            'subscription_type_id' => $subscriptionType ? $subscriptionType->id : null,
            'type' => $subscriptionType ? $subscriptionType->slug : ($data['type'] ?? null),
            'start_date' => $data['start_date'],
            'end_date' => null,
            'total_days' => $totalDays,
            'remaining_days' => $totalDays,
            'price' => $priceUsd,
            'currency' => $data['currency'],
            'price_fc' => $priceFc,
            'status' => 'pending',
        ]);

        $subscription->load('agent');
        // Ensure a client user exists for this subscription (tests expect user creation at store)
        $client = $existingClient;
        $whatsappLink = null;
        if (! $client) {
            $tempPassword = Str::random(10);
            $client = User::create([
                'name' => $subscription->client_name,
                'email' => $subscription->client_email,
                'phone' => $subscription->client_phone,
                'role' => 'client',
                'password' => Hash::make($tempPassword),
                'password_changed_at' => null,
                'is_active' => true,
            ]);
            $subscription->update(['client_id' => $client->id]);
        }

        User::where('role', 'admin')->get()->each(fn ($admin) => $admin->notify(new SubscriptionPending($subscription)));

        $redirect = redirect()->route('agent.subscriptions.index')
            ->with('success', 'Abonnement créé avec succès. En attente de validation par l\'administrateur.');

        if ($whatsappLink) {
            $redirect = $redirect->with('whatsapp_link', $whatsappLink);
        }

        return $redirect;
    }

    public function generateCredentials(Request $request, Subscription $subscription, WhatsAppService $whatsapp)
    {
        if ($subscription->agent_id !== $request->user()->id) {
            abort(403);
        }

        if ($subscription->status !== 'active') {
            return redirect()->route('agent.subscriptions.index')
                ->with('error', 'L\'abonnement doit être validé par l\'administrateur avant de générer les identifiants.');
        }

        if ($subscription->hasCredentialsGenerated()) {
            return redirect()->route('agent.subscriptions.index')
                ->with('error', 'Les identifiants ont déjà été générés pour cet abonnement.');
        }

        if ($subscription->client_id !== null) {
            return redirect()->route('agent.subscriptions.index')
                ->with('error', 'Ce client possède déjà un compte.');
        }

        $existingUser = User::where('email', $subscription->client_email)->first();
        if ($existingUser) {
            return redirect()->route('agent.subscriptions.index')
                ->with('error', 'Un utilisateur avec cet email existe déjà. Modifiez l\'email via le lien "Modifier email/tél.".');
        }

        $existingPhone = User::where('phone', $subscription->client_phone)->first();
        if ($existingPhone) {
            return redirect()->route('agent.subscriptions.index')
                ->with('error', 'Un utilisateur avec ce numéro de téléphone existe déjà. Modifiez le téléphone via le lien "Modifier email/tél.".');
        }

        $tempPassword = Str::random(10);
        $client = User::create([
            'name' => $subscription->client_name,
            'email' => $subscription->client_email,
            'phone' => $subscription->client_phone,
            'role' => 'client',
            'password' => Hash::make($tempPassword),
            'password_changed_at' => null,
            'is_active' => true,
        ]);

        $subscription->update([
            'client_id' => $client->id,
            'credentials_generated_at' => now(),
        ]);

        $client->notify(new CredentialsGenerated($subscription));
        User::where('role', 'admin')->get()->each(fn ($admin) => $admin->notify(new CredentialsGenerated($subscription)));

        $whatsappLink = $whatsapp->credentialsLink($subscription->client_phone, $client->email, $tempPassword);

        return redirect()->route('agent.subscriptions.index')
            ->with('success', 'Identifiants générés. Email: '.$client->email.' | Mot de passe temporaire: '.$tempPassword)
            ->with('whatsapp_link', $whatsappLink);
    }

    public function updateClientInfo(Request $request, Subscription $subscription)
    {
        if ($subscription->agent_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'client_email' => ['required', 'email', 'unique:users,email'],
            'client_phone' => ['required', 'string', 'max:50'],
        ]);

        $subscription->update([
            'client_email' => $data['client_email'],
            'client_phone' => $data['client_phone'],
        ]);

        return redirect()->route('agent.subscriptions.index')
            ->with('success', 'Informations client mises à jour. Vous pouvez maintenant générer les identifiants.');
    }
}
