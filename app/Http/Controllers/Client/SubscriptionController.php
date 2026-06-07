<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionSuspension;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subscriptions = Subscription::where('client_id', $request->user()->id)
            ->with('agent')
            ->latest()
            ->paginate(15);
        return view('client.subscriptions.index', compact('subscriptions'));
    }

    public function show(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->client_id === $request->user()->id, 403);

        $subscription->load(['agent', 'suspensions']);
        return view('client.subscriptions.show', compact('subscription'));
    }

    public function renew(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->client_id === $request->user()->id, 403);

        $data = $request->validate([
            'type' => ['required', 'in:weekly,monthly'],
        ]);

        $days = $data['type'] === 'weekly' ? 7 : 30;
        $subscription->type = $data['type'];
        $subscription->start_date = now();
        $subscription->end_date = now()->addDays($days);
        $subscription->total_days += $days;
        $subscription->remaining_days += $days;
        $subscription->status = 'active';
        $subscription->save();

        return redirect()->route('client.subscriptions.index')->with('success', 'Abonnement renouvelé.');
    }

    public function suspend(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->client_id === $request->user()->id, 403);

        $data = $request->validate([
            'reason' => ['required', 'string'],
            'duration_days' => ['required', 'integer', 'min:1'],
        ]);

        SubscriptionSuspension::create([
            'subscription_id' => $subscription->id,
            'reason' => $data['reason'],
            'duration_days' => $data['duration_days'],
            'status' => 'pending',
        ]);

        return redirect()->route('client.subscriptions.index')->with('success', 'Demande de suspension envoyée.');
    }

    public function reactivate(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->client_id === $request->user()->id, 403);

        $subscription->status = 'active';
        $subscription->save();

        return redirect()->route('client.subscriptions.index')->with('success', 'Abonnement réactivé.');
    }
}
