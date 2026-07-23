<?php

namespace App\Services;

use App\Models\HourlyCompensationPoint;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RecoveryBonusService
{
    public const BONUS_POINTS = 1;

    public const BONUS_INTERVAL_HOURS = 5;

    public function isActive(): bool
    {
        return now()->lessThanOrEqualTo($this->endTime());
    }

    public function endTime(): Carbon
    {
        return now()->copy()->endOfWeek(Carbon::SUNDAY);
    }

    public function awardBonus(): int
    {
        if (! $this->isActive()) {
            return 0;
        }

        $periodKey = $this->currentPeriodKey();
        $awardedAt = now();
        $rows = [];

        User::where('is_active', true)
            ->whereIn('role', ['agent', 'livreur'])
            ->select(['id', 'role'])
            ->chunkById(200, function ($users) use (&$rows, $periodKey, $awardedAt): void {
                foreach ($users as $user) {
                    $rows[] = [
                        'user_id' => $user->id,
                        'role' => $user->role,
                        'points' => self::BONUS_POINTS,
                        'period_key' => $periodKey,
                        'awarded_at' => $awardedAt,
                    ];
                }
            });

        if ($rows === []) {
            return 0;
        }

        return HourlyCompensationPoint::query()->insertOrIgnore($rows);
    }

    public function currentPeriodKey(): string
    {
        $blockStart = intdiv(now()->timestamp, self::BONUS_INTERVAL_HOURS * 3600) * self::BONUS_INTERVAL_HOURS * 3600;

        return Carbon::createFromTimestamp($blockStart)->format('Y-m-d-H-i');
    }

    public function getCompensationPoints(int $userId): int
    {
        return (int) HourlyCompensationPoint::where('user_id', $userId)->sum('points');
    }

    public function getTodayCompensationPoints(int $userId): int
    {
        return (int) HourlyCompensationPoint::where('user_id', $userId)
            ->whereDate('awarded_at', today())
            ->sum('points');
    }

    public function getCompensationPointsForDate(int $userId, Carbon $date): int
    {
        return (int) HourlyCompensationPoint::where('user_id', $userId)
            ->whereDate('awarded_at', $date)
            ->sum('points');
    }

    public function sendBonusNotification(): int
    {
        if (Cache::get('recovery_bonus_notified')) {
            return 0;
        }

        $notificationService = app(NotificationService::class);
        $count = 0;

        User::where('is_active', true)
            ->whereIn('role', ['agent', 'livreur'])
            ->chunkById(200, function ($users) use ($notificationService, &$count): void {
                foreach ($users as $user) {
                    $notificationService->notify(
                        $user,
                        'reward',
                        'Bonus exceptionnel Green Express',
                        "Afin de compenser la perte récente de certaines données, Green Express accorde exceptionnellement 1 point toutes les 5 heures jusqu'à dimanche à 23h59.\n\nÀ partir de lundi, le système de récompenses reprendra son fonctionnement habituel :\n- Les agents gagneront des points uniquement après la validation complète des commandes de leurs clients.\n- Les livreurs gagneront des points uniquement après la validation complète des livraisons.",
                        'recovery_bonus',
                    );
                    $count++;
                }
            });

        if ($count > 0) {
            Cache::put('recovery_bonus_notified', true, now()->addDays(7));
        }

        return $count;
    }
}
