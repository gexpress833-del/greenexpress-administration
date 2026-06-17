<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentPoint;
use App\Models\Commission;
use App\Services\CommissionService;
use App\Services\CurrencyService;
use App\Services\PointService;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    public function index(Request $request, PointService $pointService)
    {
        $user = $request->user();

        $totalPoints = $pointService->getTotalPoints($user->id);
        $todayPoints = $pointService->getTodayPoints($user->id);
        $totalValueUsd = round($totalPoints * PointService::VALUE_PER_POINT_USD, 2);
        $todayValueUsd = round($todayPoints * PointService::VALUE_PER_POINT_USD, 2);

        $pointsHistory = AgentPoint::where('agent_id', $user->id)
            ->with('order')
            ->latest('earned_at')
            ->paginate(15);

        $weeklyPoints = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyPoints[$date->locale('fr')->isoFormat('ddd')] = AgentPoint::where('agent_id', $user->id)
                ->whereDate('earned_at', $date)
                ->sum('points') ?: 0;
        }

        $totalCommissionsUsd = Commission::where('agent_id', $user->id)
            ->where('type', 'daily_commission')
            ->sum('amount_usd') ?: 0;

        $availableBalance = app(CommissionService::class)->getAvailableBalance($user->id);
        $minWithdrawal = CommissionService::MIN_WITHDRAWAL_USD;
        $currencyService = new CurrencyService();
        $availableBalanceFc = $currencyService->usdToFc($availableBalance);
        $minWithdrawalFc = $currencyService->usdToFc($minWithdrawal);

        return view('agent.points.index', compact(
            'totalPoints',
            'todayPoints',
            'totalValueUsd',
            'todayValueUsd',
            'pointsHistory',
            'weeklyPoints',
            'totalCommissionsUsd',
            'availableBalance',
            'availableBalanceFc',
            'minWithdrawal',
            'minWithdrawalFc'
        ));
    }
}
