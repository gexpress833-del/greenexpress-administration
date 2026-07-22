<?php

namespace App\Models;

use App\Helpers\DateHelper;
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
        'expiration_notified_at',
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
            'expiration_notified_at' => 'datetime',
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

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'subscription_id');
    }

    public function agentPoints(): HasMany
    {
        return $this->hasMany(AgentPoint::class);
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

    public function consumedDays(): int
    {
        if (! $this->start_date) {
            return 0;
        }

        $today = today()->startOfDay();
        $start = $this->start_date->copy()->startOfDay();

        if ($today->lt($start)) {
            return 0;
        }

        $end = $this->end_date ? $this->end_date->copy()->startOfDay() : $today;
        $lastCountedDate = min($today, $end);
        $date = $start->copy();
        $consumedDays = 0;

        while ($date->lte($lastCountedDate)) {
            if (DateHelper::isBusinessDay($date)) {
                $consumedDays++;
            }

            $date->addDay();
        }

        return max(0, min($consumedDays, DateHelper::subscriptionDeliveryDays($this->total_days ?? 0)));
    }

    public function daysRemaining(): int
    {
        if (! $this->end_date) {
            return $this->remaining_days ?? 0;
        }

        $remaining = today()->startOfDay()->diffInDays($this->end_date->startOfDay(), false);

        return max(0, (int) $remaining);
    }

    public function isExpiringSoon(int $days = 3): bool
    {
        return $this->status === 'active' && $this->end_date !== null && today()->diffInDays($this->end_date, false) <= $days;
    }

    public function hasCredentialsGenerated(): bool
    {
        return $this->credentials_generated_at !== null;
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

    private static array $statusTransitions = [
        'pending' => ['active', 'expired', 'rejected'],
        'active' => ['suspended', 'expired'],
        'suspended' => ['active', 'expired', 'pending'],
        'expired' => [],
        'rejected' => [],
    ];

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::$statusTransitions[$this->status] ?? [], true);
    }

    public function transitionTo(string $status, array $extra = []): bool
    {
        if (! $this->canTransitionTo($status)) {
            throw new \DomainException("Invalid subscription status transition from {$this->status} to {$status}.");
        }

        $this->status = $status;
        $this->fill($extra);

        return $this->save();
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
