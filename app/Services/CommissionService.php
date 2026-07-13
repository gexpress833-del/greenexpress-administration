<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\ExchangeRate;
use App\Models\Order;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CommissionService
{
    /**
     * Commission journalière max (USD) — protège la trésorerie au lancement.
     */
    public const DAILY_CAP_USD = 15.00;

    /**
     * Taux de commission sur le total journalier validé.
     */
    public const COMMISSION_RATE = 0.03; // 3%

    /**
     * Montant minimum de retrait.
     */
    public const MIN_WITHDRAWAL_USD = 10.00;

    /**
     * Calcule et crée la commission journalière pour un agent.
     * Appelée par le scheduler chaque soir.
     */
    public function calculateDailyCommission(User $agent, ?Carbon $date = null): ?Commission
    {
        if (! $agent->isAgent()) {
            return null;
        }

        $date = $date ?? today();

        // Vérifier si déjà calculée
        $existing = Commission::where('agent_id', $agent->id)
            ->where('calculated_for_date', $date)
            ->where('type', 'daily_commission')
            ->first();

        if ($existing) {
            return $existing;
        }

        $totalValidated = Order::where('agent_id', $agent->id)
            ->where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->whereDate('client_validated_at', $date)
            ->sum('total_amount');

        if ($totalValidated <= 0) {
            return null;
        }

        $commissionAmount = round((float) $totalValidated * self::COMMISSION_RATE, 2);
        $description = "Commission journalière du {$date->format('d/m/Y')} — 3% sur \$".number_format($totalValidated, 2);

        // Plafond journalier
        $todayTotal = Commission::where('agent_id', $agent->id)
            ->whereDate('calculated_for_date', $date)
            ->where('type', 'daily_commission')
            ->sum('amount_usd');

        $remainingCap = self::DAILY_CAP_USD - (float) $todayTotal;
        if ($remainingCap <= 0) {
            $commissionAmount = 0;
            $description .= ' (plafond journalier atteint)';
        } elseif ($commissionAmount > $remainingCap) {
            $commissionAmount = $remainingCap;
            $description .= ' (ajusté au plafond journalier)';
        }

        $rate = ExchangeRate::current();

        return Commission::create([
            'agent_id' => $agent->id,
            'order_id' => null,
            'calculated_for_date' => $date,
            'type' => 'daily_commission',
            'points' => 0,
            'amount_usd' => $commissionAmount,
            'amount_fc' => round($commissionAmount * $rate, 2),
            'description' => $description,
        ]);
    }

    /**
     * Calcule les commissions pour tous les agents pour une date donnée.
     */
    public function calculateAllDailyCommissions(?Carbon $date = null): array
    {
        $date = $date ?? today();
        $results = [];

        $agents = User::where('role', 'agent')->where('is_active', true)->get();

        foreach ($agents as $agent) {
            try {
                $commission = $this->calculateDailyCommission($agent, $date);
                $results[$agent->id] = [
                    'agent' => $agent->name,
                    'amount_usd' => $commission?->amount_usd ?? 0,
                    'created' => $commission !== null,
                ];
            } catch (\Throwable $e) {
                Log::error("Erreur commission journalière agent {$agent->id}: ".$e->getMessage());
                $results[$agent->id] = [
                    'agent' => $agent->name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    public function getAvailableBalance(int $agentId): float
    {
        $totalCommissions = Commission::where('agent_id', $agentId)
            ->where('type', 'daily_commission')
            ->sum('amount_usd') ?: 0;

        $totalWithdrawn = Withdrawal::where('agent_id', $agentId)
            ->whereIn('status', ['approved', 'paid'])
            ->sum('amount_usd') ?: 0;

        return max(0, (float) $totalCommissions - (float) $totalWithdrawn);
    }
}
