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

    public static function calculateSubscriptionDates(Carbon|string $startDate, int $totalDays): array
    {
        $start = $startDate instanceof Carbon ? $startDate->copy() : Carbon::parse($startDate);
        $end = self::addBusinessDays($start, $totalDays);

        return [
            'start_date' => $start,
            'end_date' => $end,
            'total_days' => $totalDays,
            'remaining_days' => $totalDays,
        ];
    }
}
