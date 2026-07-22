<?php

namespace App\Http\Controllers\Livreur;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPoint;
use App\Services\RecoveryBonusService;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    public function index(Request $request, RecoveryBonusService $bonusService)
    {
        $user = $request->user();

        $totalPoints = (int) DeliveryPoint::where('livreur_id', $user->id)->sum('points')
            + $bonusService->getCompensationPoints($user->id);
        $todayPoints = (int) DeliveryPoint::where('livreur_id', $user->id)
            ->whereDate('created_at', today())
            ->sum('points')
            + $bonusService->getTodayCompensationPoints($user->id);

        $pointsHistory = DeliveryPoint::where('livreur_id', $user->id)
            ->with('delivery.order')
            ->latest('created_at')
            ->paginate(15);

        $weeklyPoints = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyPoints[$date->locale('fr')->isoFormat('ddd')] = (int) DeliveryPoint::where('livreur_id', $user->id)
                ->whereDate('created_at', $date)
                ->sum('points')
                + $bonusService->getCompensationPointsForDate($user->id, $date);
        }

        return view('livreur.points.index', compact(
            'totalPoints',
            'todayPoints',
            'pointsHistory',
            'weeklyPoints'
        ));
    }
}
