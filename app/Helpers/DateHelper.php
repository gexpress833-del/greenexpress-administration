<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function isBusinessDay(Carbon $date): bool
    {
        return ! $date->isWeekend();
    }

    public static function addBusinessDays(Carbon $startDate, int $businessDays): Carbon
    {
        $date = $startDate->copy();
        $count = 0;

        while ($count < $businessDays) {
            if (self::isBusinessDay($date)) {
                $count++;
            }

            if ($count < $businessDays) {
                $date->addDay();
            }
        }

        return $date;
    }

    public static function subscriptionDeliveryDays(int $totalDays): int
    {
        return match ($totalDays) {
            7 => 5,
            30 => 20,
            default => self::countBusinessDaysWithinCalendarDays(Carbon::today(), $totalDays),
        };
    }

    public static function countBusinessDaysWithinCalendarDays(Carbon|string $startDate, int $totalDays): int
    {
        $start = $startDate instanceof Carbon ? $startDate->copy() : Carbon::parse($startDate);
        $date = $start->copy();
        $businessDays = 0;

        for ($day = 0; $day < $totalDays; $day++) {
            if (self::isBusinessDay($date)) {
                $businessDays++;
            }

            $date->addDay();
        }

        return $businessDays;
    }

    public static function calculateSubscriptionDates(Carbon|string $startDate, int $totalDays): array
    {
        $start = $startDate instanceof Carbon ? $startDate->copy() : Carbon::parse($startDate);
        $end = $start->copy()->addDays(max(0, $totalDays - 1));

        return [
            'start_date' => $start,
            'end_date' => $end,
            'total_days' => $totalDays,
            'remaining_days' => $totalDays,
        ];
    }
}
