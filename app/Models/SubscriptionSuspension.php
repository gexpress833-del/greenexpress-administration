<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['subscription_id', 'status', 'reason', 'duration_days', 'suspension_start', 'suspension_end', 'admin_notes', 'processed_by', 'processed_at'])]
class SubscriptionSuspension extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'duration_days' => 'integer',
            'suspension_start' => 'date',
            'suspension_end' => 'date',
            'processed_at' => 'datetime',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200',
            'accepted' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200',
            'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
        };
    }
}
