<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'meal_id', 'quantity', 'unit_price', 'unit_price_fc', 'total_price', 'total_price_fc'])]
class OrderItem extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'unit_price_fc' => 'decimal:2',
            'total_price' => 'decimal:2',
            'total_price_fc' => 'decimal:2',
        ];
    }

    public function getUnitPriceFcAttribute($value): float
    {
        if ((float) $value > 0) {
            return (float) $value;
        }
        return (float) $this->unit_price * ExchangeRate::current();
    }

    public function getTotalPriceFcAttribute($value): float
    {
        if ((float) $value > 0) {
            return (float) $value;
        }
        return (float) $this->total_price * ExchangeRate::current();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
