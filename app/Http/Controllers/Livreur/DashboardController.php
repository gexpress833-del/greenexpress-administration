<?php

namespace App\Http\Controllers\Livreur;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryReview;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = today();

        // All today's deliveries (visible to every livreur)
        $todayDeliveriesQuery = Delivery::whereHas('order', fn ($q) => $q->whereDate('delivery_date', $today))
            ->with(['order.items.meal', 'order.client', 'livreur'])
            ->whereNotIn('status', ['delivered', 'cancelled']);

        $todayDeliveries = $todayDeliveriesQuery->count();
        $availableTodayDeliveries = (clone $todayDeliveriesQuery)->whereNull('livreur_id')->latest()->get();
        $myTodayDeliveries = Delivery::where('livreur_id', $user->id)
            ->whereHas('order', fn ($q) => $q->whereDate('delivery_date', $today))
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->with(['order.items.meal', 'order.client'])
            ->latest()
            ->get();
        $deliveredToday = Delivery::where('livreur_id', $user->id)
            ->whereHas('order', fn ($q) => $q->whereDate('delivery_date', $today))
            ->where('status', 'delivered')
            ->count();
        $pendingDeliveries = Delivery::where('livreur_id', $user->id)->whereIn('status', ['assigned', 'picked_up', 'in_transit'])->count();
        $totalDelivered = Delivery::where('livreur_id', $user->id)->where('status', 'delivered')->count();
        $totalDeliveries = Delivery::where('livreur_id', $user->id)->count();
        $performanceRate = $totalDeliveries > 0 ? round(($totalDelivered / $totalDeliveries) * 100, 1) : 0;

        $weeklyDeliveries = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyDeliveries[$date->locale('fr')->isoFormat('ddd')] = Delivery::where('livreur_id', $user->id)->whereDate('delivered_at', $date)->count();
        }

        $recentDeliveries = Delivery::where('livreur_id', $user->id)->with('order')->latest()->take(5)->get();

        $averageRating = DeliveryReview::where('livreur_id', $user->id)->avg('rating') ?? 0;
        $totalReviews = DeliveryReview::where('livreur_id', $user->id)->count();
        $recentReviews = DeliveryReview::where('livreur_id', $user->id)
            ->with('order', 'client')
            ->latest()
            ->take(5)
            ->get();

        return view('livreur.dashboard', compact(
            'todayDeliveries', 'availableTodayDeliveries', 'myTodayDeliveries',
            'deliveredToday', 'pendingDeliveries',
            'totalDelivered', 'totalDeliveries', 'performanceRate',
            'weeklyDeliveries', 'recentDeliveries',
            'averageRating', 'totalReviews', 'recentReviews'
        ));
    }
}
