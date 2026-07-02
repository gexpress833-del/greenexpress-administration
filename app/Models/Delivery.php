<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['order_id', 'livreur_id', 'delivery_code', 'status', 'picked_up_at', 'delivered_at', 'notes'])]
class Delivery extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'picked_up_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function livreur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'livreur_id');
    }

    public function deliveryPoints(): HasMany
    {
        return $this->hasMany(DeliveryPoint::class);
    }

    public function hasDeliveryPoints(): bool
    {
        return $this->deliveryPoints()->exists();
    }

    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            'assigned' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200',
            'picked_up' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200',
            'in_transit' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200',
            'delivered' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
        };
    }
}
