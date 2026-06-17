<?php

namespace App\Http\Controllers\Livreur;

use App\Http\Controllers\Controller;
use App\Models\LivreurPoint;
use App\Services\CurrencyService;
use App\Services\LivreurPointService;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    public function index(Request $request, LivreurPointService $pointService)
    {
        $user = $request->user();

        $totalPoints = $pointService->getTotalPoints($user->id);
        $todayPoints = $pointService->getTodayPoints($user->id);
        $totalValueUsd = round($totalPoints * LivreurPointService::VALUE_PER_POINT_USD, 2);
        $todayValueUsd = round($todayPoints * LivreurPointService::VALUE_PER_POINT_USD, 2);
        $availableBalance = $pointService->getAvailableBalance($user->id);

        $currencyService = new CurrencyService();
        $totalValueFc = $currencyService->usdToFc($totalValueUsd);
        $todayValueFc = $currencyService->usdToFc($todayValueUsd);
        $availableBalanceFc = $currencyService->usdToFc($availableBalance);
        $minWithdrawal = LivreurPointService::MIN_WITHDRAWAL_USD;
        $minWithdrawalFc = $currencyService->usdToFc($minWithdrawal);
        $exchangeRate = $currencyService->getRate();

        $pointsHistory = LivreurPoint::where('livreur_id', $user->id)
            ->with(['order', 'delivery'])
            ->latest('earned_at')
            ->paginate(15);

        $weeklyPoints = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyPoints[$date->locale('fr')->isoFormat('ddd')] = LivreurPoint::where('livreur_id', $user->id)
                ->whereDate('earned_at', $date)
                ->sum('points') ?: 0;
        }

        return view('livreur.points.index', compact(
            'totalPoints',
            'todayPoints',
            'totalValueUsd',
            'todayValueUsd',
            'availableBalance',
            'totalValueFc',
            'todayValueFc',
            'availableBalanceFc',
            'minWithdrawal',
            'minWithdrawalFc',
            'exchangeRate',
            'pointsHistory',
            'weeklyPoints'
        ));
    }
}
