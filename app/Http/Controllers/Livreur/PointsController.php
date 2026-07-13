<?php

namespace App\Http\Controllers\Livreur;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPoint;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $totalPoints = DeliveryPoint::where('livreur_id', $user->id)->sum('points') ?: 0;
        $todayPoints = DeliveryPoint::where('livreur_id', $user->id)
            ->whereDate('created_at', today())
            ->sum('points') ?: 0;

        $pointsHistory = DeliveryPoint::where('livreur_id', $user->id)
            ->with('delivery.order')
            ->latest('created_at')
            ->paginate(15);

        $weeklyPoints = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyPoints[$date->locale('fr')->isoFormat('ddd')] = DeliveryPoint::where('livreur_id', $user->id)
                ->whereDate('created_at', $date)
                ->sum('points') ?: 0;
        }

        return view('livreur.points.index', compact(
            'totalPoints',
            'todayPoints',
            'pointsHistory',
            'weeklyPoints'
        ));
    }
}
