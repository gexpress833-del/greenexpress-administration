<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Notifications\SubscriptionActivated;
use App\Services\ActivityLogService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        // Calculate actual dates based on validation date
        $validationDate = now();
        
        // If requested start date is in the past, use validation date as start date
        $requestedStartDate = $subscription->start_date ? \Carbon\Carbon::parse($subscription->start_date) : null;
        $actualStartDate = ($requestedStartDate && $requestedStartDate->lt($validationDate)) 
            ? $validationDate 
            : ($requestedStartDate ?? $validationDate);
        
        // Calculate end date using business days
        $dates = DateHelper::calculateSubscriptionDates($actualStartDate, $subscription->total_days);
        
        $subscription->status = 'active';
        $subscription->admin_validated_at = $validationDate;
        $subscription->validated_by = $request->user()->id;
        $subscription->start_date = $dates['start_date'];
        $subscription->end_date = $dates['end_date'];
        $subscription->total_days = $dates['total_days'];
        $subscription->remaining_days = $dates['remaining_days'];
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
