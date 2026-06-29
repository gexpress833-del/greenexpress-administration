<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Calculate end date by adding business days (excluding weekends)
     * The start date is counted as the first business day if it's a weekday
     *
     * @param Carbon $startDate
     * @param int $businessDays
     * @return Carbon
     */
    public static function addBusinessDays(Carbon $startDate, int $businessDays): Carbon
    {
        $currentDate = $startDate->copy();
        $daysAdded = 0;

        while ($daysAdded < $businessDays) {
            // Count the current day if it's a business day
            if (!$currentDate->isWeekend()) {
                $daysAdded++;
            }
            
            // Move to next day only if we haven't reached the target
            if ($daysAdded < $businessDays) {
                $currentDate->addDay();
            }
        }

        return $currentDate;
    }

    /**
     * Calculate the number of business days between two dates
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    public static function businessDaysBetween(Carbon $startDate, Carbon $endDate): int
    {
        $days = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lt($endDate)) {
            if (!$currentDate->isWeekend()) {
                $days++;
            }
            $currentDate->addDay();
        }

        return $days;
    }

    /**
     * Calculate subscription dates based on validation date
     * Weekly: 5 business days (Monday to Friday)
     * Monthly: 20 business days (4 weeks)
     *
     * @param Carbon $validationDate
     * @param int $durationDays
     * @return array ['start_date' => Carbon, 'end_date' => Carbon, 'total_days' => int, 'remaining_days' => int]
     */
    public static function calculateSubscriptionDates(Carbon $validationDate, int $durationDays): array
    {
        // Start date is the validation date
        $startDate = $validationDate->copy();
        
        // Calculate end date by adding business days
        $endDate = self::addBusinessDays($startDate, $durationDays);
        
        // Total days is the duration (already in business days)
        $totalDays = $durationDays;
        
        // Calculate remaining business days from today
        $today = Carbon::now();
        $remainingDays = $today->lt($endDate) 
            ? self::businessDaysBetween($today, $endDate) 
            : 0;

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'remaining_days' => $remainingDays,
        ];
    }
}
