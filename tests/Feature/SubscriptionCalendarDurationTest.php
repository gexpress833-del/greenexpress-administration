<?php

namespace Tests\Feature;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Tests\TestCase;

class SubscriptionCalendarDurationTest extends TestCase
{
    public function test_weekly_subscription_keeps_seven_calendar_days_and_five_delivery_days(): void
    {
        $dates = DateHelper::calculateSubscriptionDates(Carbon::parse('2026-07-06'), 7);

        $this->assertSame('2026-07-06', $dates['start_date']->toDateString());
        $this->assertSame('2026-07-12', $dates['end_date']->toDateString());
        $this->assertSame(7, $dates['total_days']);
        $this->assertSame(5, DateHelper::subscriptionDeliveryDays($dates['total_days']));
    }

    public function test_monthly_subscription_keeps_thirty_calendar_days_and_twenty_delivery_days(): void
    {
        $dates = DateHelper::calculateSubscriptionDates(Carbon::parse('2026-07-06'), 30);

        $this->assertSame('2026-07-06', $dates['start_date']->toDateString());
        $this->assertSame('2026-08-04', $dates['end_date']->toDateString());
        $this->assertSame(30, $dates['total_days']);
        $this->assertSame(20, DateHelper::subscriptionDeliveryDays($dates['total_days']));
    }
}
