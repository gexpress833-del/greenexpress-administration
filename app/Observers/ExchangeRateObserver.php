<?php

namespace App\Observers;

use App\Models\ExchangeRate;
use App\Services\NotificationService;

class ExchangeRateObserver
{
    public function created(ExchangeRate $rate): void
    {
        NotificationService::notifyAllUsers(
            'Nouveau taux de change',
            "Le taux de change {$rate->from_currency}/{$rate->to_currency} a été défini à {$rate->rate}",
            'exchange_rate',
            ExchangeRate::class,
            $rate->id
        );
    }

    public function updated(ExchangeRate $rate): void
    {
        if ($rate->wasChanged('rate')) {
            NotificationService::notifyAllUsers(
                'Taux de change mis à jour',
                "Le taux {$rate->from_currency}/{$rate->to_currency} est maintenant de {$rate->rate}",
                'exchange_rate',
                ExchangeRate::class,
                $rate->id
            );
        }
    }
}
