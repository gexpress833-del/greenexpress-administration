<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['code', 'client_validation_code', 'agent_id', 'client_id', 'client_name', 'client_phone', 'delivery_address', 'delivery_date', 'total_amount', 'currency', 'total_amount_fc', 'status', 'notes', 'confirmed_at', 'delivered_at', 'client_validated_at'])]
class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'total_amount_fc' => 'decimal:2',
            'delivery_date' => 'date',
            'confirmed_at' => 'datetime',
            'delivered_at' => 'datetime',
            'client_validated_at' => 'datetime',
        ];
    }

    public function getTotalAmountFcAttribute($value): float
    {
        if ((float) $value > 0) {
            return (float) $value;
        }
        return (float) $this->total_amount * ExchangeRate::current();
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function commission(): HasOne
    {
        return $this->hasOne(Commission::class);
    }

    public function agentPoints(): HasMany
    {
        return $this->hasMany(AgentPoint::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(DeliveryReview::class);
    }

    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200',
            'confirmed' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200',
            'preparing' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200',
            'delivering' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200',
            'delivered' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200',
            'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
        };
    }
}
