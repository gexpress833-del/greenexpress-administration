<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $activeSubscription = Subscription::where('client_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->with('subscriptionType')
            ->latest()
            ->first();

        // Historique des abonnements
        $subscriptionHistory = Subscription::where('client_id', $user->id)
            ->with('subscriptionType')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Total payé pour les abonnements
        $totalSubscriptionSpent = Subscription::where('client_id', $user->id)
            ->whereIn('status', ['active', 'expired'])
            ->sum('price');
        $totalSubscriptionSpentFc = Subscription::where('client_id', $user->id)
            ->whereIn('status', ['active', 'expired'])
            ->sum('price_fc');

        $totalOrders = Order::where('client_id', $user->id)->count();
        $totalSpent = Order::where('client_id', $user->id)->whereIn('status', ['delivered', 'confirmed'])->sum('total_amount');
        $totalSpentFc = Order::where('client_id', $user->id)->whereIn('status', ['delivered', 'confirmed'])->sum('total_amount_fc');
        $pendingOrders = Order::where('client_id', $user->id)->whereNotIn('status', ['delivered', 'cancelled'])->count();
        $deliveredOrders = Order::where('client_id', $user->id)->where('status', 'delivered')->count();
        $upcomingDeliveries = Order::where('client_id', $user->id)
            ->where('delivery_date', '>=', today())
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->orderBy('delivery_date')
            ->take(3)
            ->get();
        $recentOrders = Order::where('client_id', $user->id)->latest()->take(5)->get();

        $weeklyMenu = [];
        $currentWeekLabel = '';
        $todayOrder = null;
        if ($activeSubscription?->subscriptionType) {
            $startDate = $activeSubscription->start_date;
            $daysDiff = $startDate ? (int) $startDate->startOfDay()->diffInDays(today()->startOfDay(), false) : 0;
            $weekNumber = max(1, (int) floor($daysDiff / 7) + 1);
            $weekNumber = (($weekNumber - 1) % 4) + 1;
            $currentWeekLabel = 'Semaine '.$weekNumber;

            $dayLabels = ['monday' => 'Lundi', 'tuesday' => 'Mardi', 'wednesday' => 'Mercredi', 'thursday' => 'Jeudi', 'friday' => 'Vendredi'];
            $dayOffsets = ['monday' => 0, 'tuesday' => 1, 'wednesday' => 2, 'thursday' => 3, 'friday' => 4];
            $weekStart = today()->copy()->startOfWeek(Carbon::MONDAY);
            foreach ($dayOffsets as $day => $offset) {
                $date = $weekStart->copy()->addDays($offset);
                $meal = $activeSubscription->subscriptionType->mealForDate($date, $startDate);
                $weeklyMenu[] = [
                    'label' => $dayLabels[$day],
                    'meal' => $meal?->name ?? 'Non défini',
                    'isToday' => $date->isToday(),
                ];
            }

            // Commande du jour avec code de validation
            $todayOrder = Order::where('client_id', $user->id)
                ->where('subscription_id', $activeSubscription->id)
                ->whereDate('delivery_date', today())
                ->with('delivery', 'items.meal')
                ->first();
        }

        return view('client.dashboard', compact(
            'activeSubscription', 'subscriptionHistory', 'totalSubscriptionSpent', 'totalSubscriptionSpentFc',
            'totalOrders', 'totalSpent', 'totalSpentFc',
            'pendingOrders', 'deliveredOrders', 'upcomingDeliveries', 'recentOrders', 'weeklyMenu', 'currentWeekLabel',
            'todayOrder'
        ));
    }
}
