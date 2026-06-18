<?php

namespace App\Services;

use App\Models\AgentPoint;
use App\Models\Order;
use App\Models\Withdrawal;

class PointService
{
    public const POINTS_PER_ORDER = 12;
    public const POINTS_FOR_SMALL_ORDER = 3;
    public const SMALL_ORDER_LIMIT_FC = 5000;
    public const VALUE_PER_POINT_USD = 0.025; // 12 pts = 0.30$

    /**
     * Montant minimum de retrait pour un agent (basé sur les points).
     * TEMPORAIREMENT À 0 POUR LES TESTS. À REMETTRE À 10.00 APRÈS.
     */
    public const MIN_WITHDRAWAL_USD = 0.00;

    public function creditForOrder(Order $order): ?AgentPoint
    {
        if ($order->status !== 'delivered' || $order->client_validated_at === null || ! $order->agent_id) {
            return null;
        }

        $existingPoint = AgentPoint::where('order_id', $order->id)
            ->where('agent_id', $order->agent_id)
            ->first();

        if ($existingPoint) {
            return $existingPoint;
        }

        $points = $order->total_amount_fc < self::SMALL_ORDER_LIMIT_FC
            ? self::POINTS_FOR_SMALL_ORDER
            : self::POINTS_PER_ORDER;
        $valueUsd = round($points * self::VALUE_PER_POINT_USD, 2);

        return AgentPoint::create([
            'agent_id' => $order->agent_id,
            'order_id' => $order->id,
            'points' => $points,
            'value_usd' => $valueUsd,
            'description' => "Points gagnés pour la commande {$order->code}",
            'earned_at' => now(),
        ]);
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

    /**
     * Solde disponible au retrait : valeur totale des points - retraits approuvés/payés.
     */
    public function getAvailableBalance(int $agentId): float
    {
        $totalValue = (float) (AgentPoint::where('agent_id', $agentId)->sum('value_usd') ?: 0);

        $totalWithdrawn = Withdrawal::where('agent_id', $agentId)
            ->whereIn('status', ['approved', 'paid'])
            ->sum('amount_usd') ?: 0;

        return max(0, round($totalValue - (float) $totalWithdrawn, 2));
    }
}
