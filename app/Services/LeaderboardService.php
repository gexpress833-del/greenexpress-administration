<?php

namespace App\Services;

use App\Enums\PeriodType;
use App\Models\Commission;
use App\Models\LeaderboardEntry;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaderboardService
{
    public function calculateWeekly(Carbon $date): void
    {
        $start = $date->copy()->startOfWeek();
        $end = $date->copy()->endOfWeek();

        $this->calculateForPeriod(PeriodType::WEEK, $start, $end);
    }

    public function calculateMonthly(Carbon $date): void
    {
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();

        $this->calculateForPeriod(PeriodType::MONTH, $start, $end);
    }

    protected function calculateForPeriod(PeriodType $periodType, Carbon $start, Carbon $end): void
    {
        $agents = User::where('role', 'agent')->where('is_active', true)->get();

        // Métrique : commandes validées
        $ordersData = Order::where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->whereBetween('client_validated_at', [$start, $end])
            ->selectRaw('agent_id, count(*) as total')
            ->groupBy('agent_id')
            ->pluck('total', 'agent_id');

        // Métrique : commissions
        $commissionsData = Commission::where('type', 'daily_commission')
            ->whereBetween('calculated_for_date', [$start, $end])
            ->selectRaw('agent_id, sum(amount_usd) as total')
            ->groupBy('agent_id')
            ->pluck('total', 'agent_id');

        // Métrique : points
        $pointsData = DB::table('agent_points')
            ->whereBetween('earned_at', [$start, $end])
            ->selectRaw('agent_id, sum(points) as total')
            ->groupBy('agent_id')
            ->pluck('total', 'agent_id');

        $metrics = [
            'orders' => $ordersData,
            'commission' => $commissionsData,
            'points' => $pointsData,
        ];

        foreach ($metrics as $metricType => $data) {
            $sorted = $data->sortDesc();
            $rank = 1;

            foreach ($sorted as $agentId => $value) {
                LeaderboardEntry::updateOrCreate(
                    [
                        'agent_id' => $agentId,
                        'period_type' => $periodType->value,
                        'period_start' => $start,
                        'period_end' => $end,
                        'metric_type' => $metricType,
                    ],
                    [
                        'value' => $value,
                        'rank' => $rank,
                    ]
                );
                $rank++;
            }
        }
    }

    public function getTop10(PeriodType $periodType, string $metricType): array
    {
        $entries = LeaderboardEntry::with('agent')
            ->where('period_type', $periodType->value)
            ->where('metric_type', $metricType)
            ->orderByDesc('value')
            ->take(10)
            ->get();

        return $entries->toArray();
    }
}
