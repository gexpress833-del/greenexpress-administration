<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\LivreurPoint;
use App\Models\Withdrawal;

class LivreurPointService
{
    /**
     * Points par tranche de 5 000 FC du montant total de la commande livrée.
     */
    public const POINTS_PER_TIER = 2;

    /**
     * Points de base pour la première tranche (bonus livreur).
     */
    public const BASE_POINTS = 2;

    /**
     * Seuil d'une tranche en FC.
     */
    public const TIER_LIMIT_FC = 5000;

    /**
     * Points maximum par livraison.
     */
    public const MAX_POINTS = 10;

    /**
     * Valeur d'un point en USD (identique au système agent).
     */
    public const VALUE_PER_POINT_USD = 0.025;

    /**
     * Montant minimum de retrait pour un livreur.
     */
    public const MIN_WITHDRAWAL_USD = 7.00;

    /**
     * Crédite les points au livreur pour une livraison validée.
     * Idempotent : ne crédite qu'une seule fois par livraison.
     */
    public function creditForDelivery(Delivery $delivery): ?LivreurPoint
    {
        $order = $delivery->order;

        if (! $order || $order->status !== 'delivered' || $order->client_validated_at === null) {
            return null;
        }

        if (! $delivery->livreur_id) {
            return null;
        }

        $existing = LivreurPoint::where('livreur_id', $delivery->livreur_id)
            ->where('delivery_id', $delivery->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $tiers = max(1, 1 + (int) floor($order->total_amount_fc / self::TIER_LIMIT_FC));
        $points = min(self::MAX_POINTS, self::BASE_POINTS + (self::POINTS_PER_TIER * $tiers));
        $valueUsd = round($points * self::VALUE_PER_POINT_USD, 2);

        return LivreurPoint::create([
            'livreur_id' => $delivery->livreur_id,
            'order_id' => $order->id,
            'delivery_id' => $delivery->id,
            'points' => $points,
            'value_usd' => $valueUsd,
            'description' => "Points gagnés pour la livraison de la commande {$order->code}",
            'earned_at' => now(),
        ]);
    }

    public function getTotalPoints(int $livreurId): int
    {
        return LivreurPoint::where('livreur_id', $livreurId)->sum('points') ?: 0;
    }

    public function getTodayPoints(int $livreurId): int
    {
        return LivreurPoint::where('livreur_id', $livreurId)
            ->whereDate('earned_at', today())
            ->sum('points') ?: 0;
    }

    public function getTotalValueUsd(int $livreurId): float
    {
        return round((float) (LivreurPoint::where('livreur_id', $livreurId)->sum('value_usd') ?: 0), 2);
    }

    /**
     * Montant total déjà retiré (approuvé/payé).
     */
    public function getTotalWithdrawn(int $livreurId): float
    {
        return round((float) (Withdrawal::where('livreur_id', $livreurId)
            ->whereIn('status', ['approved', 'paid'])
            ->sum('amount_usd') ?: 0), 2);
    }

    /**
     * Solde disponible au retrait : valeur totale des points - retraits approuvés/payés.
     */
    public function getAvailableBalance(int $livreurId): float
    {
        $totalValue = $this->getTotalValueUsd($livreurId);
        $totalWithdrawn = $this->getTotalWithdrawn($livreurId);
        return max(0, round($totalValue - $totalWithdrawn, 2));
    }

    /**
     * Nombre de points disponibles après déduction des retraits.
     */
    public function getAvailablePoints(int $livreurId): int
    {
        return (int) floor($this->getAvailableBalance($livreurId) / self::VALUE_PER_POINT_USD);
    }
}
