<?php

namespace App\Http\Controllers\Client;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionSuspension;
use App\Models\User;
use App\Notifications\SubscriptionReactivated;
use App\Notifications\SubscriptionRenewed;
use App\Notifications\SubscriptionSuspended;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        $subscription->load(['agent', 'suspensions', 'subscriptionType', 'orders.items.meal', 'orders.delivery']);

        $today = now()->startOfDay();
        $tomorrow = $today->copy()->addDay();

        $todayOrder = $subscription->orders
            ->where('delivery_date', '>=', $today)
            ->where('delivery_date', '<', $today->copy()->addDay())
            ->first();

        $tomorrowOrder = $subscription->orders
            ->where('delivery_date', '>=', $tomorrow)
            ->where('delivery_date', '<', $tomorrow->copy()->addDay())
            ->first();

        $history = $subscription->orders
            ->where('delivery_date', '<', $today)
            ->sortByDesc('delivery_date');

        $weeklyMenu = [];
        $monthlyMenu = [];
        $dayLabels = ['monday' => 'Lundi', 'tuesday' => 'Mardi', 'wednesday' => 'Mercredi', 'thursday' => 'Jeudi', 'friday' => 'Vendredi'];
        $startDate = $subscription->start_date;

        foreach ($dayLabels as $day => $label) {
            $meal = $subscription->subscriptionType?->{$day.'Meal'};
            $weeklyMenu[] = ['label' => $label, 'meal' => $meal?->name ?? 'Non défini'];
        }

        $baseWeek = $startDate?->copy()->startOfWeek(Carbon::MONDAY);
        $daysDiff = $startDate ? (int) $startDate->startOfDay()->diffInDays(today()->startOfDay(), false) : 0;
        $currentWeek = max(1, (int) floor($daysDiff / 7) + 1);
        $currentWeek = (($currentWeek - 1) % 4) + 1;

        for ($week = 1; $week <= 4; $week++) {
            $weekDays = [];
            $i = 0;
            foreach ($dayLabels as $day => $label) {
                $date = $baseWeek?->copy()->addWeeks($week - 1)->addDays($i);
                $meal = $subscription->subscriptionType?->mealForDate($date, $startDate);
                $weekDays[] = ['label' => $label, 'meal' => $meal?->name ?? 'Non défini'];
                $i++;
            }
            $monthlyMenu[] = ['week' => $week, 'days' => $weekDays];
        }

        $consumedDays = $subscription->consumedDays();
        $totalDays = $subscription->total_days ?? 1;
        $deliveryDays = DateHelper::subscriptionDeliveryDays($totalDays);
        $progress = min(100, round(($consumedDays / max(1, $deliveryDays)) * 100));

        return view('client.subscriptions.show', compact(
            'subscription',
            'todayOrder',
            'tomorrowOrder',
            'history',
            'weeklyMenu',
            'monthlyMenu',
            'currentWeek',
            'consumedDays',
            'totalDays',
            'deliveryDays',
            'progress'
        ));
    }

    public function renew(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->client_id === $request->user()->id, 403);

        $data = $request->validate([
            'type' => ['required', 'in:weekly,monthly'],
        ]);

        $days = $data['type'] === 'weekly' ? 7 : 30;

        $newSubscription = Subscription::create([
            'client_id' => $subscription->client_id,
            'agent_id' => $subscription->agent_id,
            'subscription_type_id' => $subscription->subscription_type_id,
            'type' => $data['type'],
            'start_date' => now(),
            'end_date' => now(),
            'total_days' => $days,
            'remaining_days' => $days,
            'price' => $subscription->price,
            'currency' => $subscription->currency,
            'price_fc' => $subscription->price_fc,
            'status' => 'pending',
            'client_name' => $subscription->client_name,
            'client_phone' => $subscription->client_phone,
            'client_email' => $subscription->client_email,
        ]);

        try {
            $newSubscription->client->notify(new SubscriptionRenewed($newSubscription));
            $newSubscription->agent?->notify(new SubscriptionRenewed($newSubscription));
            User::where('role', 'admin')->get()->each(fn ($admin) => $admin->notify(new SubscriptionRenewed($newSubscription)));
        } catch (\Throwable $e) {
            Log::error('SubscriptionRenewed notification failed', [
                'subscription_id' => $newSubscription->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Notification catégorisée au client
        app(NotificationService::class)->clientSubscriptionRenewalSent($request->user(), $newSubscription);

        return redirect()->route('client.subscriptions.index')->with('success', 'Demande de renouvellement envoyée avec succès. Vous serez notifié dès validation par l\'administrateur.');
    }

    public function suspend(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->client_id === $request->user()->id, 403);

        $data = $request->validate([
            'reason' => ['required', 'string'],
            'duration_days' => ['required', 'integer', 'min:1'],
        ]);

        $suspension = SubscriptionSuspension::create([
            'subscription_id' => $subscription->id,
            'reason' => $data['reason'],
            'duration_days' => $data['duration_days'],
            'status' => 'pending',
        ]);

        $suspension->load('subscription.client', 'subscription.agent');
        try {
            $suspension->subscription->client->notify(new SubscriptionSuspended($suspension));
            $suspension->subscription->agent?->notify(new SubscriptionSuspended($suspension));
            User::where('role', 'admin')->get()->each(fn ($admin) => $admin->notify(new SubscriptionSuspended($suspension)));
        } catch (\Throwable $e) {
            Log::error('SubscriptionSuspended notification failed', [
                'subscription_id' => $suspension->subscription_id,
                'error' => $e->getMessage(),
            ]);
        }

        // Notification catégorisée au client
        app(NotificationService::class)->clientSubscriptionSuspended($request->user(), $subscription, $data['reason'], $data['duration_days']);

        return redirect()->route('client.subscriptions.index')->with('success', 'Demande de suspension envoyée avec succès.');
    }

    public function reactivate(Request $request, Subscription $subscription)
    {
        abort_unless($subscription->client_id === $request->user()->id, 403);

        $subscription->transitionTo('pending', [
            'admin_validated_at' => null,
            'validated_by' => null,
        ]);

        try {
            $subscription->client->notify(new SubscriptionReactivated($subscription));
            $subscription->agent?->notify(new SubscriptionReactivated($subscription));
            User::where('role', 'admin')->get()->each(fn ($admin) => $admin->notify(new SubscriptionReactivated($subscription)));
        } catch (\Throwable $e) {
            Log::error('SubscriptionReactivated notification failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Notification catégorisée au client
        app(NotificationService::class)->clientSubscriptionReactivated($request->user(), $subscription);

        return redirect()->route('client.subscriptions.index')->with('success', 'Demande de réactivation envoyée avec succès.');
    }
}
