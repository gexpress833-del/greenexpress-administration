<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Notifications\SubscriptionActivated;
use App\Services\ActivityLogService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subscriptions = Subscription::with(['client', 'agent', 'subscriptionType'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->search.'%';
                $q->whereHas('client', fn ($c) => $c->where('name', 'like', $term))
                    ->orWhereHas('agent', fn ($a) => $a->where('name', 'like', $term));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['client', 'agent', 'validator', 'suspensions', 'subscriptionType']);

        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $subscription->status = 'active';
        $subscription->admin_validated_at = now();
        $subscription->validated_by = $request->user()->id;
        $subscription->save();

        if ($subscription->agent) {
            $subscription->agent->notify(new SubscriptionActivated($subscription));
        }

        app(ActivityLogService::class)->logFromRequest($request, 'subscription_validated', Subscription::class, $subscription->id, 'Admin validated subscription for client '.($subscription->client?->name ?? $subscription->client_name));

        $redirect = redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'Abonnement validé.');

        $clientPhone = $subscription->client?->phone ?? $subscription->client_phone;
        if ($clientPhone) {
            $whatsappLink = app(WhatsAppService::class)->subscriptionActivatedLink(
                $clientPhone,
                $subscription->client?->name ?? $subscription->client_name,
                $subscription->type,
                $subscription->end_date->format('d/m/Y')
            );
            $redirect->with('whatsapp_link', $whatsappLink);
        }

        return $redirect;
    }
}
