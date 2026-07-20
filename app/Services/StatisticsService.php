<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    public function getDashboardKpi(?Carbon $start = null, ?Carbon $end = null): array
    {
        $start = $start ?? today()->subDays(30);
        $end = $end ?? today();

        $ordersQuery = Order::whereBetween('created_at', [$start, $end]);
        $validatedQuery = Order::where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->whereBetween('client_validated_at', [$start, $end]);

        $totalOrders = (clone $ordersQuery)->count();
        $validatedOrders = (clone $validatedQuery)->count();
        $cancelledOrders = (clone $ordersQuery)->where('status', 'cancelled')->count();
        $totalRevenue = (float) (clone $validatedQuery)->sum('total_amount');

        $avgDeliveryCost = $validatedOrders > 0
            ? round($totalRevenue / $validatedOrders, 2)
            : 0;

        $cancellationRate = $totalOrders > 0
            ? round(($cancelledOrders / $totalOrders) * 100, 2)
            : 0;

        $withdrawalsPaid = (float) Withdrawal::whereIn('status', ['approved', 'paid'])
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount_usd');

        $profitEstimate = round($totalRevenue * 0.25 - $withdrawalsPaid, 2);

        $topAgents = User::where('role', 'agent')
            ->where('is_active', true)
            ->withCount(['ordersAsAgent' => fn ($q) => $q->where('status', 'delivered')->whereNotNull('client_validated_at')->whereBetween('client_validated_at', [$start, $end])])
            ->orderByDesc('orders_as_agent_count')
            ->take(10)
            ->get();

        $profitableZones = Order::where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->whereBetween('client_validated_at', [$start, $end])
            ->selectRaw('delivery_address, count(*) as orders_count, sum(total_amount) as total_revenue')
            ->groupBy('delivery_address')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $expiredSubscriptions = Subscription::where('status', 'expired')->count();

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $renewalsThisMonth = Subscription::where('created_at', '>=', $monthStart)
            ->where('created_at', '<=', $monthEnd)
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('subscriptions as s2')
                    ->whereColumn('s2.client_id', 'subscriptions.client_id')
                    ->whereColumn('s2.id', '<', 'subscriptions.id');
            })
            ->count();

        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $weeklyRevenue = (float) Subscription::where('status', 'active')
            ->whereBetween('admin_validated_at', [$weekStart, $weekEnd])
            ->sum('price');
        $monthlyRevenue = (float) Subscription::where('status', 'active')
            ->whereBetween('admin_validated_at', [$monthStart, $monthEnd])
            ->sum('price');

        $mealsDelivered = Order::where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->whereBetween('client_validated_at', [$start, $end])
            ->count();

        $mealsNotPickedUp = Order::whereIn('status', ['confirmed', 'preparing', 'delivering'])
            ->whereNotNull('admin_validated_at')
            ->where('delivery_date', '<', today())
            ->count();

        $totalExpired = $expiredSubscriptions + Subscription::onlyTrashed()->count();
        $renewalRate = $totalExpired > 0
            ? round(($renewalsThisMonth / $totalExpired) * 100, 2)
            : 0;

        $newSubscribers = Subscription::whereBetween('created_at', [$start, $end])
            ->distinct('client_id')
            ->count('client_id');

        return [
            'period' => [
                'start' => $start->format('d/m/Y'),
                'end' => $end->format('d/m/Y'),
            ],
            'orders' => [
                'total' => $totalOrders,
                'validated' => $validatedOrders,
                'cancelled' => $cancelledOrders,
                'cancellation_rate' => $cancellationRate,
                'lost' => $totalOrders - $validatedOrders - $cancelledOrders,
            ],
            'financial' => [
                'total_revenue_usd' => $totalRevenue,
                'avg_delivery_cost' => $avgDeliveryCost,
                'commissions_paid' => 0,
                'withdrawals_paid' => $withdrawalsPaid,
                'profit_estimate' => $profitEstimate,
            ],
            'subscriptions' => [
                'active' => $activeSubscriptions,
                'expired' => $expiredSubscriptions,
                'renewals_this_month' => $renewalsThisMonth,
                'weekly_revenue' => $weeklyRevenue,
                'monthly_revenue' => $monthlyRevenue,
                'meals_delivered' => $mealsDelivered,
                'meals_not_picked_up' => $mealsNotPickedUp,
                'renewal_rate' => $renewalRate,
                'new_subscribers' => $newSubscribers,
            ],
            'top_agents' => $topAgents,
            'profitable_zones' => $profitableZones,
        ];
    }

    public function getAgentPerformance(int $agentId, ?Carbon $start = null, ?Carbon $end = null): array
    {
        $start = $start ?? today()->subDays(30);
        $end = $end ?? today();

        $validatedQuery = Order::where('agent_id', $agentId)
            ->where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->whereBetween('client_validated_at', [$start, $end]);

        $ordersCount = (clone $validatedQuery)->count();
        $totalRevenue = (float) (clone $validatedQuery)->sum('total_amount');
        $totalCommissions = 0;
        $totalPoints = DB::table('agent_points')->where('agent_id', $agentId)->sum('points') ?: 0;

        $dailyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyTrend[$date->locale('fr')->isoFormat('ddd')] = Order::where('agent_id', $agentId)
                ->where('status', 'delivered')
                ->whereNotNull('client_validated_at')
                ->whereDate('client_validated_at', $date)
                ->count();
        }

        return [
            'orders_count' => $ordersCount,
            'total_revenue' => $totalRevenue,
            'total_commissions' => $totalCommissions,
            'total_points' => (int) $totalPoints,
            'daily_trend' => $dailyTrend,
        ];
    }
}
