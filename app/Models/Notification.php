<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'notifiable_type',
        'notifiable_id',
        'url',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'meal' => '🍽️',
            'subscription_type' => '📋',
            'exchange_rate' => '💱',
            'order' => '📦',
            'delivery' => '🚚',
            default => '🔔',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'meal' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-200',
            'subscription_type' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200',
            'exchange_rate' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200',
            'order' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-200',
            'delivery' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }
}
