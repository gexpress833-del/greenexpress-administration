<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['agent_id', 'livreur_id', 'amount_usd', 'amount_fc', 'status', 'notes', 'processed_by', 'processed_at'])]
class Withdrawal extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'amount_usd' => 'decimal:2',
            'amount_fc' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function livreur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'livreur_id');
    }

    /**
     * Bénéficiaire du retrait : agent ou livreur.
     */
    public function beneficiary(): ?User
    {
        return $this->agent ?? $this->livreur;
    }

    public function getBeneficiaryRoleAttribute(): string
    {
        return $this->livreur_id ? 'Livreur' : 'Agent';
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
}
