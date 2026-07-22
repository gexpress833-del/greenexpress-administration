<?php

use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionExpiringSoon;
use App\Notifications\SubscriptionExpiringSoonAdmin;
use App\Services\NotificationService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $now = now();

    Subscription::where('status', 'active')
        ->whereNotNull('end_date')
        ->where('end_date', '<', $now->startOfDay())
        ->update(['status' => 'expired']);

    $expiring = Subscription::where('status', 'active')
        ->whereNotNull('end_date')
        ->whereBetween('end_date', [$now->startOfDay(), $now->copy()->addDays(3)->endOfDay()])
        ->whereNull('expiration_notified_at')
        ->get();

    $notificationService = app(NotificationService::class);

    foreach ($expiring as $subscription) {
        $subscription->client?->notify(new SubscriptionExpiringSoon($subscription));
        User::where('role', 'admin')->get()->each(fn ($admin) => $admin->notify(new SubscriptionExpiringSoonAdmin($subscription)));

        // Notification catégorisée au client
        if ($subscription->client) {
            $daysRemaining = $subscription->daysRemaining();
            $notificationService->clientSubscriptionExpiring($subscription->client, $subscription, $daysRemaining);
        }

        $subscription->update(['expiration_notified_at' => $now]);
    }
})->daily();

Schedule::command('app:award-recovery-bonus')
    ->cron('0 */5 * * *')
    ->withoutOverlapping();
