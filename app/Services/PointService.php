<?php

namespace App\Services;

use App\Models\AgentPoint;
use App\Models\Order;

class PointService
{
    public const AGENT_POINTS_PER_ORDER = 12;

    public const LIVREUR_POINTS_PER_ORDER = 13;

    public const VALUE_PER_POINT_USD = 0.025;

    public static function pointsPerOrderFor(string $role): int
    {
        return match ($role) {
            'agent' => self::AGENT_POINTS_PER_ORDER,
            'livreur' => self::LIVREUR_POINTS_PER_ORDER,
            default => 0,
        };
    }

    public function creditForOrder(Order $order): ?AgentPoint
    {
        if ($order->status !== 'delivered' || $order->client_validated_at === null || ! $order->agent_id) {
            return null;
        }

        $points = self::AGENT_POINTS_PER_ORDER;
        $valueUsd = round($points * self::VALUE_PER_POINT_USD, 2);

        return AgentPoint::firstOrCreate(
            [
                'agent_id' => $order->agent_id,
                'order_id' => $order->id,
            ],
            [
                'points' => $points,
                'value_usd' => $valueUsd,
                'description' => "Points gagnés pour la commande {$order->code}",
                'earned_at' => now(),
            ],
        );
    }

    public function getTotalPoints(int $agentId): int
    {
        return AgentPoint::where('agent_id', $agentId)->sum('points') ?: 0;
    }

    public function getTodayPoints(int $agentId): int
    {
        return AgentPoint::where('agent_id', $agentId)
            ->whereDate('earned_at', today())
            ->sum('points') ?: 0;
    }

    public function getTodayValidatedOrdersCount(int $agentId): int
    {
        return Order::where('agent_id', $agentId)
            ->where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->whereDate('client_validated_at', today())
            ->count();
    }
}
