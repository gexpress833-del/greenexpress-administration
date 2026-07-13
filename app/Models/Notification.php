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
        'category',
        'notifiable_type',
        'notifiable_id',
        'url',
        'whatsapp_link',
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
        return match ($this->category) {
            'reward' => '🏆',
            'delivery' => '🚚',
            'subscription' => '📄',
            'order' => '🛒',
            'badge' => '🏅',
            'bonus' => '🎁',
            'success' => '✅',
            'alert' => '⚠️',
            default => match ($this->type) {
                'meal' => '🍽️',
                'subscription_type' => '📋',
                'exchange_rate' => '💱',
                'order' => '📦',
                'delivery' => '🚚',
                default => 'ℹ️',
            },
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->category) {
            'reward' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200',
            'delivery' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200',
            'subscription' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200',
            'order' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-200',
            'badge' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-200',
            'bonus' => 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-200',
            'success' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200',
            'alert' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'reward' => 'Récompense',
            'delivery' => 'Livraison',
            'subscription' => 'Abonnement',
            'order' => 'Commande',
            'badge' => 'Badge',
            'bonus' => 'Bonus',
            'success' => 'Succès',
            'alert' => 'Alerte',
            default => 'Information',
        };
    }

    public static function getCategoryMainType(string $category): string
    {
        return match ($category) {
            'reward', 'badge', 'bonus' => 'congratulations',
            'alert' => 'alert',
            'success' => 'success',
            'delivery', 'subscription', 'order', 'information' => 'information',
            default => 'information',
        };
    }
}
