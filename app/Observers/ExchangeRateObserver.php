<?php

namespace App\Observers;

use App\Models\ExchangeRate;
use App\Services\NotificationService;
use App\Jobs\RefreshExchangeRateValues;

class ExchangeRateObserver
{
    public function created(ExchangeRate $rate): void
    {
        // Dispatch async job (or sync if env requests immediate execution without worker)
        $rateValue = (float) $rate->rate;
        if (env('EXCHANGE_RATE_REFRESH_SYNC', false)) {
            RefreshExchangeRateValues::dispatchSync($rateValue);
        } else {
            RefreshExchangeRateValues::dispatch($rateValue);
        }
        NotificationService::notifyAllUsers(
            'Nouveau taux de change',
            "Le taux de change {$rate->currency_from}/{$rate->currency_to} a été défini à {$rate->rate}",
            'exchange_rate',
            ExchangeRate::class,
            $rate->id
        );
    }

    public function updated(ExchangeRate $rate): void
    {
        if ($rate->wasChanged('rate')) {
            // Dispatch async recomputation job (or sync if configured)
            $rateValue = (float) $rate->rate;
            if (env('EXCHANGE_RATE_REFRESH_SYNC', false)) {
                RefreshExchangeRateValues::dispatchSync($rateValue);
            } else {
                RefreshExchangeRateValues::dispatch($rateValue);
            }

            NotificationService::notifyAllUsers(
                'Taux de change mis à jour',
                "Le taux {$rate->currency_from}/{$rate->currency_to} est maintenant de {$rate->rate}",
                'exchange_rate',
                ExchangeRate::class,
                $rate->id
            );
        }
    }
}
