<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
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
        return view('agent.subscriptions.create');
    }

    public function store(Request $request, WhatsAppService $whatsapp)
    {
        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_phone' => ['required', 'string', 'max:50'],
            'client_email' => ['required', 'email', 'unique:users,email'],
            'type' => ['required', 'in:weekly,monthly'],
            'start_date' => ['required', 'date'],
            'currency' => ['required', 'in:usd,fc'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $tempPassword = Str::random(10);
        $client = User::create([
            'name' => $data['client_name'],
            'email' => $data['client_email'],
            'phone' => $data['client_phone'],
            'role' => 'client',
            'password' => Hash::make($tempPassword),
        ]);

        $totalDays = $data['type'] === 'weekly' ? 7 : 30;
        $price = (float) $data['price'];
        $currencyService = app(CurrencyService::class);
        $priceFc = $data['currency'] === 'fc' ? $price : $currencyService->usdToFc($price);
        $priceUsd = $data['currency'] === 'usd' ? $price : $currencyService->fcToUsd($price);

        $subscription = Subscription::create([
            'client_id' => $client->id,
            'agent_id' => $request->user()->id,
            'type' => $data['type'],
            'start_date' => $data['start_date'],
            'end_date' => now()->parse($data['start_date'])->addDays($totalDays),
            'total_days' => $totalDays,
            'remaining_days' => $totalDays,
            'price' => $priceUsd,
            'currency' => $data['currency'],
            'price_fc' => $priceFc,
            'status' => 'pending',
        ]);

        $whatsappLink = $whatsapp->credentialsLink($data['client_phone'], $client->email, $tempPassword);

        return redirect()->route('agent.subscriptions.index')
            ->with('success', 'Abonnement créé. Identifiant: ' . $client->email . ' | Mot de passe temporaire: ' . $tempPassword)
            ->with('whatsapp_link', $whatsappLink);
    }
}
