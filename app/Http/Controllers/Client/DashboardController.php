<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Subscription;
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

        return view('client.dashboard', compact(
            'activeSubscription', 'subscriptionHistory', 'totalSubscriptionSpent', 'totalSubscriptionSpentFc',
            'totalOrders', 'totalSpent', 'totalSpentFc',
            'pendingOrders', 'deliveredOrders', 'upcomingDeliveries', 'recentOrders'
        ));
    }
}
