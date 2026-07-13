<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = ['rate', 'currency_from', 'currency_to'];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
        ];
    }

    public static function current(): float
    {
        $rate = self::where('currency_from', 'USD')
            ->where('currency_to', 'FC')
            ->latest()
            ->first();

        return (float) ($rate?->rate ?? config('app.usd_to_fc_rate', 2800));
    }
}
