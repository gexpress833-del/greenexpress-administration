<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'client_id', 'type', 'description', 'status', 'admin_response', 'resolved_by', 'resolved_at'])]
class Complaint extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            'open' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200',
            'in_progress' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200',
            'resolved' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200',
            'rejected' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'missing_item' => 'Article manquant',
            'wrong_item' => 'Mauvais article',
            'late_delivery' => 'Livraison en retard',
            'quality_issue' => 'Problème de qualité',
            'other' => 'Autre',
            default => $this->type,
        };
    }
}
