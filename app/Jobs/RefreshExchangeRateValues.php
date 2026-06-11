<?php

namespace App\Jobs;

use App\Models\Meal;
use App\Models\SubscriptionType;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefreshExchangeRateValues implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public float $rate;

    /**
     * Create a new job instance.
     */
    public function __construct(float $rate)
    {
        $this->rate = $rate;
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $newRate = (float) $this->rate;

        Log::info('RefreshExchangeRateValues job started, rate=' . $newRate);

        DB::transaction(function () use ($newRate) {
            Meal::chunkById(100, function ($meals) use ($newRate) {
                foreach ($meals as $meal) {
                    $meal->price_fc = round((float) $meal->price * $newRate, 2);
                    $meal->save();
                }
            });

            SubscriptionType::chunkById(100, function ($types) use ($newRate) {
                foreach ($types as $type) {
                    $type->price_fc = round((float) $type->price * $newRate, 2);
                    $type->save();
                }
            });

            OrderItem::chunkById(200, function ($items) use ($newRate) {
                foreach ($items as $item) {
                    $item->unit_price_fc = round((float) $item->unit_price * $newRate, 2);
                    $item->total_price_fc = round((float) $item->total_price * $newRate, 2);
                    $item->save();
                }
            });

            Order::chunkById(200, function ($orders) use ($newRate) {
                foreach ($orders as $order) {
                    $order->total_amount_fc = round((float) $order->total_amount * $newRate, 2);
                    $order->save();
                }
            });

            Withdrawal::chunkById(200, function ($withs) use ($newRate) {
                foreach ($withs as $w) {
                    $w->amount_fc = round((float) $w->amount_usd * $newRate, 2);
                    $w->save();
                }
            });
        });

        Log::info('RefreshExchangeRateValues job finished');
    }
}
