<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Notifications\SubscriptionActivated;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use App\Services\SubscriptionDeliveryService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
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
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
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
        $validationDate = now();
        $requestedStartDate = $subscription->start_date ? Carbon::parse($subscription->start_date) : null;
        $actualStartDate = ($requestedStartDate && $requestedStartDate->lt($validationDate))
            ? $validationDate
            : ($requestedStartDate ?? $validationDate);

        $activeSub = Subscription::where('client_id', $subscription->client_id)
            ->where('id', '!=', $subscription->id)
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '>', $validationDate)
            ->latest('end_date')
            ->first();

        if ($activeSub) {
            $actualStartDate = Carbon::parse($activeSub->end_date)->addDay();
        }

        $dates = DateHelper::calculateSubscriptionDates($actualStartDate, $subscription->total_days);

        $subscription->status = 'active';
        $subscription->admin_validated_at = $validationDate;
        $subscription->validated_by = $request->user()->id;
        $subscription->start_date = $dates['start_date'];
        $subscription->end_date = $dates['end_date'];
        $subscription->total_days = $dates['total_days'];
        $subscription->remaining_days = $dates['remaining_days'];
        $subscription->save();

        $deliveryService = app(SubscriptionDeliveryService::class);
        $deliveryService->rewardAgent($subscription);
        $deliveryService->generateDailyOrders($subscription);

        // Notification à l'agent selon le contexte (création ou renouvellement)
        if ($subscription->agent) {
            $notificationService = app(NotificationService::class);
            $isRenewal = Subscription::where('client_id', $subscription->client_id)
                ->where('id', '!=', $subscription->id)
                ->exists();

            if ($isRenewal) {
                $notificationService->agentSubscriptionRenewed($subscription->agent, $subscription);
            } else {
                $points = $subscription->total_days <= 7 ? 25 : 50;
                $notificationService->agentSubscriptionValidated($subscription->agent, $subscription, $points);
            }
        }

        // Notification au client
        if ($subscription->client) {
            app(NotificationService::class)->clientSubscriptionValidated($subscription->client, $subscription);
        }

        if ($subscription->agent) {
            try {
                $subscription->agent->notify(new SubscriptionActivated($subscription));
            } catch (\Throwable $e) {
                Log::error('SubscriptionActivated notification failed', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        app(ActivityLogService::class)->logFromRequest($request, 'subscription_validated', Subscription::class, $subscription->id, 'Admin validated subscription for client '.($subscription->client?->name ?? $subscription->client_name));

        $redirect = redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', $activeSub
                ? 'Abonnement validé. Il entrera en vigueur le '.$dates['start_date']->format('d/m/Y').' après la fin de l\'abonnement actuel.'
                : 'Abonnement validé avec succès.');

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

    public function reject(Request $request, Subscription $subscription)
    {
        try {
            $subscription->status = 'rejected';
            $subscription->admin_validated_at = now();
            $subscription->validated_by = $request->user()->id;
            $subscription->save();

            app(ActivityLogService::class)->logFromRequest($request, 'subscription_rejected', Subscription::class, $subscription->id, 'Admin rejected subscription for client '.($subscription->client?->name ?? $subscription->client_name));

            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Abonnement refusé.');
        } catch (\Throwable $e) {
            Log::error('Failed to reject subscription', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'Une erreur est survenue lors du refus de l\'abonnement.');
        }
    }

    public function expiring(Request $request)
    {
        $days = (int) $request->get('days', 3);
        $threshold = now()->addDays($days)->endOfDay();

        $subscriptions = Subscription::with(['client', 'agent', 'subscriptionType'])
            ->where(function ($q) use ($threshold) {
                $q->where('status', 'active')
                    ->whereNotNull('end_date')
                    ->where('end_date', '<=', $threshold);
            })
            ->orWhere('status', 'expired')
            ->orderBy('end_date')
            ->paginate(20)
            ->withQueryString();

        return view('admin.subscriptions.expiring', compact('subscriptions', 'days'));
    }

    public function destroy(Request $request, Subscription $subscription)
    {
        try {
            app(ActivityLogService::class)->logFromRequest($request, 'subscription_deleted', Subscription::class, $subscription->id, 'Admin deleted subscription for client '.($subscription->client?->name ?? $subscription->client_name));

            $subscription->suspensions()->delete();
            $subscription->delete();

            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Abonnement supprimé.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete subscription', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'abonnement.');
        }
    }
}
