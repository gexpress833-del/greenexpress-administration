<?php

namespace App\Services;

use App\Models\ExchangeRate;

class CurrencyService
{
    /**
     * Taux de change : 1 USD = X FC
     * Modifiable selon le marché.
     */
    protected float $rate;

    public function __construct(?float $rate = null)
    {
        $this->rate = $rate ?? ExchangeRate::current();
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function usdToFc(float $usd): float
    {
        return round($usd * $this->rate, 2);
    }

    public function fcToUsd(float $fc): float
    {
        return round($fc / $this->rate, 2);
    }

    public function formatFc(float $amount): string
    {
        return number_format($amount, 2, ',', '.').' FC';
    }

    public function formatUsd(float $amount): string
    {
        return '$ '.number_format($amount, 2);
    }
}
