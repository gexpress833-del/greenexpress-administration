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
        $todayDeliveries = Delivery::where('livreur_id', $user->id)->whereDate('created_at', today())->count();
        $deliveredToday = Delivery::where('livreur_id', $user->id)->whereDate('delivered_at', today())->count();
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
            'todayDeliveries', 'deliveredToday', 'pendingDeliveries',
            'totalDelivered', 'totalDeliveries', 'performanceRate',
            'weeklyDeliveries', 'recentDeliveries',
            'averageRating', 'totalReviews', 'recentReviews'
        ));
    }
}
