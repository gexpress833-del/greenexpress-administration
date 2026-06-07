<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['agent_id', 'type', 'order_id', 'earned_date', 'claimed_at', 'description'])]
class AgentReward extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'earned_date' => 'date',
            'claimed_at' => 'datetime',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
