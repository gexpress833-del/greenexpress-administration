<?php

namespace App\Services;

use App\Enums\RewardType;
use App\Models\AgentReward;
use App\Models\Order;
use App\Models\User;

class RewardService
{
    public const DAILY_ORDER_THRESHOLD = 10;

    public function checkDailyMealBonus(User $agent): ?AgentReward
    {
        if (! $agent->isAgent()) {
            return null;
        }

        $todayCount = Order::where('agent_id', $agent->id)
            ->where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->whereDate('client_validated_at', today())
            ->count();

        if ($todayCount < self::DAILY_ORDER_THRESHOLD) {
            return null;
        }

        // Vérifier si déjà récompensé aujourd'hui
        $alreadyRewarded = AgentReward::where('agent_id', $agent->id)
            ->where('type', RewardType::FREE_STANDARD_MEAL->value)
            ->where('earned_date', today())
            ->exists();

        if ($alreadyRewarded) {
            return null;
        }

        return AgentReward::create([
            'agent_id' => $agent->id,
            'type' => RewardType::FREE_STANDARD_MEAL->value,
            'earned_date' => today(),
            'description' => "Bonus repas gratuit pour {$todayCount} commandes validées aujourd'hui",
        ]);
    }

    public function getTodayRewardCount(int $agentId): int
    {
        return AgentReward::where('agent_id', $agentId)
            ->where('earned_date', today())
            ->count();
    }
}
