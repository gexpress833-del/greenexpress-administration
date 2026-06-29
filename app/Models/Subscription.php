<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'agent_id',
        'subscription_type_id',
        'type',
        'start_date',
        'end_date',
        'total_days',
        'remaining_days',
        'price',
        'currency',
        'price_fc',
        'status',
        'admin_validated_at',
        'validated_by',
        'client_name',
        'client_phone',
        'client_email',
        'credentials_generated_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_days' => 'integer',
            'remaining_days' => 'integer',
            'price' => 'decimal:2',
            'price_fc' => 'decimal:2',
            'admin_validated_at' => 'datetime',
            'credentials_generated_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function subscriptionType(): BelongsTo
    {
        return $this->belongsTo(SubscriptionType::class);
    }

    public function suspensions(): HasMany
    {
        return $this->hasMany(SubscriptionSuspension::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isExpired(): bool
    {
        return $this->end_date !== null && $this->end_date->isPast();
    }

    public function hasCredentialsGenerated(): bool
    {
        return $this->client_id !== null;
    }

    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200',
            'active' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200',
            'suspended' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200',
            'expired' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        if ($this->subscriptionType) {
            return $this->subscriptionType->name;
        }

        return match ($this->type) {
            'weekly' => 'Hebdomadaire',
            'monthly' => 'Mensuel',
            'bi_weekly' => 'Bi-mensuel',
            'quarterly' => 'Trimestriel',
            default => ucfirst($this->type),
        };
    }
}
