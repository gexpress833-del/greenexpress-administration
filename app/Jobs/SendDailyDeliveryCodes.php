<?php

namespace App\Jobs;

use App\Models\Order;
use App\Notifications\DailyDeliveryCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDailyDeliveryCodes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $today = today();

        Order::whereDate('delivery_date', $today)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->whereNotNull('client_id')
            ->whereNotNull('client_validation_code')
            ->with('items.meal', 'client')
            ->chunkById(200, function ($orders) use ($today): void {
                foreach ($orders as $order) {
                    $client = $order->client;
                    if (! $client) {
                        continue;
                    }

                    $alreadyNotified = $client->notifications()
                        ->where('type', DailyDeliveryCode::class)
                        ->whereDate('created_at', $today)
                        ->whereJsonContains('data->order_id', $order->id)
                        ->exists();

                    if ($alreadyNotified) {
                        continue;
                    }

                    $client->notify(new DailyDeliveryCode($order));
                }
            });
    }
}
