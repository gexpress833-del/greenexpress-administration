<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentPoint;
use App\Services\PointService;
use App\Services\RecoveryBonusService;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    public function index(Request $request, PointService $pointService, RecoveryBonusService $bonusService)
    {
        $user = $request->user();

        $totalPoints = $pointService->getTotalPoints($user->id) + $bonusService->getCompensationPoints($user->id);
        $todayPoints = $pointService->getTodayPoints($user->id) + $bonusService->getTodayCompensationPoints($user->id);
        $totalValueUsd = round($totalPoints * PointService::VALUE_PER_POINT_USD, 2);
        $todayValueUsd = round($todayPoints * PointService::VALUE_PER_POINT_USD, 2);

        $pointsHistory = AgentPoint::where('agent_id', $user->id)
            ->with('order')
            ->latest('earned_at')
            ->paginate(15);

        $weeklyPoints = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyPoints[$date->locale('fr')->isoFormat('ddd')] = (int) AgentPoint::where('agent_id', $user->id)
                ->whereDate('earned_at', $date)
                ->sum('points')
                + $bonusService->getCompensationPointsForDate($user->id, $date);
        }

        return view('agent.points.index', compact(
            'totalPoints',
            'todayPoints',
            'totalValueUsd',
            'todayValueUsd',
            'pointsHistory',
            'weeklyPoints'
        ));
    }
}
