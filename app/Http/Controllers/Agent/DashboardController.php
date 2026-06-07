<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Services\CommissionService;
use App\Services\PointService;
use App\Services\RewardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, PointService $pointService, CommissionService $commissionService, RewardService $rewardService)
    {
        $user = $request->user();

        // Statistiques basées uniquement sur commandes livrées ET validées
        $validatedQuery = Order::where('agent_id', $user->id)
            ->where('status', 'delivered')
            ->whereNotNull('client_validated_at');

        $todayOrders = (clone $validatedQuery)->whereDate('client_validated_at', today())->count();
        $totalOrders = (clone $validatedQuery)->count();

        $totalPoints = $pointService->getTotalPoints($user->id);
        $todayPoints = $pointService->getTodayPoints($user->id);
        $totalCommissions = Commission::where('agent_id', $user->id)->where('type', 'daily_commission')->sum('amount_usd');
        $availableBalance = $commissionService->getAvailableBalance($user->id);
        $pendingWithdrawals = \App\Models\Withdrawal::where('agent_id', $user->id)->where('status', 'pending')->count();

        $todayCommissionUsd = (float) Commission::where('agent_id', $user->id)
            ->where('type', 'daily_commission')
            ->whereDate('calculated_for_date', today())
            ->sum('amount_usd');
        $dailyCap = CommissionService::DAILY_CAP_USD;
        $dailyCapRemaining = max(0, $dailyCap - $todayCommissionUsd);

        $commissionsByDate = Commission::where('agent_id', $user->id)
            ->where('type', 'daily_commission')
            ->latest('calculated_for_date')
            ->take(7)
            ->get();

        $weeklyOrders = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyOrders[$date->format('D')] = Order::where('agent_id', $user->id)
                ->where('status', 'delivered')
                ->whereNotNull('client_validated_at')
                ->whereDate('client_validated_at', $date)
                ->count();
        }

        $recentOrders = Order::where('agent_id', $user->id)
            ->where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->latest('client_validated_at')
            ->take(5)
            ->get();

        $topClients = Order::where('agent_id', $user->id)
            ->where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->selectRaw('client_name, count(*) as orders_count, sum(total_amount) as total_spent, sum(total_amount_fc) as total_spent_fc')
            ->groupBy('client_name')
            ->orderByDesc('orders_count')
            ->take(5)
            ->get();

        $badges = $user->badges()->latest('earned_date')->take(5)->get();
        $todayRewards = $rewardService->getTodayRewardCount($user->id);

        return view('agent.dashboard', compact(
            'todayOrders', 'totalOrders', 'totalPoints', 'todayPoints', 'totalCommissions', 'availableBalance',
            'pendingWithdrawals', 'commissionsByDate', 'weeklyOrders',
            'recentOrders', 'topClients', 'todayCommissionUsd', 'dailyCap', 'dailyCapRemaining',
            'badges', 'todayRewards'
        ));
    }
}
