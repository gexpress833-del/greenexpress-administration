<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionTypeWeeklyMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_type_id',
        'week_number',
        'day',
        'meal_id',
    ];

    protected $casts = [
        'week_number' => 'integer',
    ];

    public function subscriptionType(): BelongsTo
    {
        return $this->belongsTo(SubscriptionType::class);
    }

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
