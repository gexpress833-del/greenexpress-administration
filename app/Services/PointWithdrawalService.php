<?php

namespace App\Services;

use App\Models\DeliveryPoint;
use App\Models\ExchangeRate;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PointWithdrawalService
{
    public const MIN_WITHDRAWAL_USD = 5.00;

    public function availablePoints(User $user): int
    {
        $earned = match ($user->role) {
            'agent' => $user->agentPoints()->sum('points'),
            'livreur' => DeliveryPoint::where('livreur_id', $user->id)->sum('points'),
            default => 0,
        };

        $reserved = Withdrawal::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'paid'])
            ->sum('points');

        return max(0, (int) $earned - (int) $reserved);
    }

    public function create(User $user, int $points, string $operator, string $number): Withdrawal
    {
        $minimumPoints = (int) ceil(self::MIN_WITHDRAWAL_USD / PointService::VALUE_PER_POINT_USD);

        if ($points < $minimumPoints) {
            throw ValidationException::withMessages([
                'points' => 'Le retrait minimum est de $ 5, soit 200 points.',
            ]);
        }

        return DB::transaction(function () use ($user, $points, $operator, $number) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            $availablePoints = $this->availablePoints($lockedUser);

            if ($points > $availablePoints) {
                throw ValidationException::withMessages([
                    'points' => 'Le nombre de points demandé dépasse votre solde disponible.',
                ]);
            }

            $amountUsd = round($points * PointService::VALUE_PER_POINT_USD, 2);

            return Withdrawal::create([
                'user_id' => $user->id,
                'agent_id' => $user->isAgent() ? $user->id : null,
                'points' => $points,
                'amount_usd' => $amountUsd,
                'amount_fc' => round($amountUsd * ExchangeRate::current(), 2),
                'mobile_money_operator' => $operator,
                'mobile_money_number' => $number,
                'status' => 'pending',
            ]);
        });
    }
}
