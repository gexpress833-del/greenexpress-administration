<?php

namespace App\Http\Controllers\Agent;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Notifications\CredentialsGenerated;
use App\Notifications\SubscriptionPending;
use App\Services\CurrencyService;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        $paymentUrlTemplate = config('services.payment.url');

        return view('agent.subscriptions.create', compact('subscriptionTypes', 'paymentUrlTemplate'));
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
            'price' => ['nullable', 'numeric', 'min:0'],
            'payment_confirmed' => ['required', 'accepted'],
        ]);

        // Resolve subscription type: prefer explicit id, fallback to slug or default mapping
        $subscriptionType = null;
        $totalDays = 0;

        if (! empty($data['subscription_type_id'])) {
            $subscriptionType = SubscriptionType::find($data['subscription_type_id']);
        } elseif (! empty($data['type'])) {
            $subscriptionType = SubscriptionType::where('slug', $data['type'])->first();
        }

        if ($subscriptionType) {
            $totalDays = $subscriptionType->duration_days;
        } else {
            $map = ['weekly' => 7, 'monthly' => 30];
            $totalDays = $map[$data['type'] ?? 'monthly'] ?? 30;
        }
        $client = User::withTrashed()->where('email', $data['client_email'])->first();
        $phoneOwner = User::withTrashed()->where('phone', $data['client_phone'])->first();

        if ($client && $phoneOwner && $phoneOwner->id !== $client->id) {
            return redirect()->route('agent.subscriptions.index')
                ->with('error', 'Cet email et ce numéro de téléphone appartiennent à deux comptes différents.');
        }

        if (! $client && $phoneOwner) {
            if ($phoneOwner->role !== 'client') {
                return redirect()->route('agent.subscriptions.index')
                    ->with('error', 'Ce numéro de téléphone appartient déjà à un compte non-client. Utilisez un autre numéro.');
            }

            $client = $phoneOwner;
        }

        if ($client && $client->role !== 'client') {
            return redirect()->route('agent.subscriptions.index')
                ->with('error', 'Ce compte existe déjà mais ce n’est pas un compte client. Utilisez un autre email ou téléphone.');
        }

        $tempPassword = null;

        try {
            $subscription = DB::transaction(function () use (&$client, &$tempPassword, $data, $request, $subscriptionType, $totalDays) {
                if ($client) {
                    if ($client->trashed()) {
                        $client->restore();
                    }

                    $client->fill([
                        'name' => $data['client_name'],
                        'email' => $data['client_email'],
                        'phone' => $data['client_phone'],
                        'is_active' => true,
                    ])->save();
                } else {
                    $tempPassword = Str::random(10);
                    $client = User::create([
                        'name' => $data['client_name'],
                        'email' => $data['client_email'],
                        'phone' => $data['client_phone'],
                        'role' => 'client',
                        'password' => Hash::make($tempPassword),
                        'password_changed_at' => null,
                        'is_active' => true,
                    ]);
                }

                $currencyService = app(CurrencyService::class);

                if ($subscriptionType) {
                    $priceUsd = (float) $subscriptionType->price;
                    $priceFc = (float) $subscriptionType->price_fc;
                    if ($priceFc <= 0) {
                        $priceFc = $currencyService->usdToFc($priceUsd);
                    }
                } else {
                    $price = (float) $data['price'];
                    $priceFc = $data['currency'] === 'fc' ? $price : $currencyService->usdToFc($price);
                    $priceUsd = $data['currency'] === 'usd' ? $price : $currencyService->fcToUsd($price);
                }
                $dates = DateHelper::calculateSubscriptionDates($data['start_date'], $totalDays);
                $legacyType = $totalDays <= 7 ? 'weekly' : 'monthly';

                return Subscription::create([
                    'client_id' => $client->id,
                    'agent_id' => $request->user()->id,
                    'client_name' => $data['client_name'],
                    'client_phone' => $data['client_phone'],
                    'client_email' => $data['client_email'],
                    'subscription_type_id' => $subscriptionType?->id,
                    'type' => $legacyType,
                    'start_date' => $dates['start_date'],
                    'end_date' => $dates['end_date'],
                    'total_days' => $dates['total_days'],
                    'remaining_days' => $dates['remaining_days'],
                    'price' => $priceUsd,
                    'currency' => $data['currency'],
                    'price_fc' => $priceFc,
                    'status' => 'pending',
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Subscription and client creation failed', [
                'agent_id' => $request->user()->id,
                'email' => $data['client_email'],
                'phone' => $data['client_phone'],
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('agent.subscriptions.index')
                ->with('error', 'Impossible de créer l’abonnement. Veuillez réessayer ou contacter l’administrateur.');
        }

        $subscription->load('agent');
        $whatsappLink = null;

        if (isset($tempPassword) && $tempPassword) {
            $whatsappLink = app(WhatsAppService::class)->credentialsLink(
                $data['client_phone'],
                $data['client_email'],
                $tempPassword
            );
        }

        try {
            User::where('role', 'admin')->get()->each(fn ($admin) => $admin->notify(new SubscriptionPending($subscription)));
        } catch (\Throwable $e) {
            Log::error('SubscriptionPending notification failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
        }

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
            $subscription->load(['client', 'agent', 'subscriptionType']);

            $pdf = Pdf::loadView('pdf.subscription-receipt', [
                'subscription' => $subscription,
                'client' => $subscription->client,
                'temporaryPassword' => null,
            ]);

            return $pdf->download('recu-abonnement-'.$subscription->id.'.pdf');
        }

        $client = $subscription->client
            ?? User::where('email', $subscription->client_email)
                ->orWhere('phone', $subscription->client_phone)
                ->first();

        if ($client && $client->role !== 'client') {
            return redirect()->route('agent.subscriptions.index')
                ->with('error', 'Ce compte existe déjà mais ce n’est pas un compte client.');
        }

        try {
            [$client, $response] = DB::transaction(function () use ($client, $subscription) {
                $isNewClient = false;
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
                    $isNewClient = true;
                } else {
                    $tempPassword = null;
                    $client->fill([
                        'name' => $subscription->client_name ?? $client->name,
                        'email' => $subscription->client_email ?? $client->email,
                        'phone' => $subscription->client_phone ?? $client->phone,
                        'is_active' => true,
                    ])->save();
                }

                $subscription->update([
                    'client_id' => $client->id,
                    'credentials_generated_at' => now(),
                ]);
                $subscription->load(['client', 'agent', 'subscriptionType']);

                $response = Pdf::loadView('pdf.subscription-receipt', [
                    'subscription' => $subscription,
                    'client' => $client,
                    'temporaryPassword' => $tempPassword,
                ])->download('recu-abonnement-'.$subscription->id.'.pdf');

                return [$client, $response];
            });
        } catch (\Throwable $e) {
            Log::error('Credential generation failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('agent.subscriptions.index')
                ->with('error', 'Impossible de générer les identifiants. Veuillez réessayer.');
        }

        try {
            $client->notify(new CredentialsGenerated($subscription));
            User::where('role', 'admin')->get()->each(fn ($admin) => $admin->notify(new CredentialsGenerated($subscription)));
        } catch (\Throwable $e) {
            Log::error('CredentialsGenerated notification failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $response;
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
