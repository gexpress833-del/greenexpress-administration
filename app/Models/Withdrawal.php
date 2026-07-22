<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'agent_id', 'points', 'amount_usd', 'amount_fc', 'mobile_money_operator', 'mobile_money_number', 'status', 'notes', 'processed_by', 'processed_at'])]
class Withdrawal extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'amount_usd' => 'decimal:2',
            'amount_fc' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200',
            'approved' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200',
            'paid' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200',
            'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
        };
    }

    private static array $statusTransitions = [
        'pending' => ['approved', 'rejected'],
        'approved' => ['paid', 'rejected'],
        'paid' => [],
        'rejected' => [],
    ];

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::$statusTransitions[$this->status] ?? [], true);
    }

    public function transitionTo(string $status, array $extra = []): bool
    {
        if (! $this->canTransitionTo($status)) {
            throw new \DomainException("Invalid withdrawal status transition from {$this->status} to {$status}.");
        }

        $this->status = $status;
        $this->fill($extra);

        return $this->save();
    }
}
