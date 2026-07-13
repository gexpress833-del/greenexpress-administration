<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'description', 'image', 'price', 'price_fc', 'category_id', 'status', 'is_active'])]
class Meal extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_fc' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function getPriceFcAttribute($value): float
    {
        if ((float) $value > 0) {
            return (float) $value;
        }

        return (float) $this->price * ExchangeRate::current();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
