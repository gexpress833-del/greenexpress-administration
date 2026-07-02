<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'price_fc',
        'duration_days',
        'currency',
        'is_active',
        'display_order',
        'monday_meal_id',
        'tuesday_meal_id',
        'wednesday_meal_id',
        'thursday_meal_id',
        'friday_meal_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_fc' => 'decimal:2',
        'duration_days' => 'integer',
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'monday_meal_id' => 'integer',
        'tuesday_meal_id' => 'integer',
        'wednesday_meal_id' => 'integer',
        'thursday_meal_id' => 'integer',
        'friday_meal_id' => 'integer',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function mondayMeal(): BelongsTo
    {
        return $this->belongsTo(Meal::class, 'monday_meal_id');
    }

    public function tuesdayMeal(): BelongsTo
    {
        return $this->belongsTo(Meal::class, 'tuesday_meal_id');
    }

    public function wednesdayMeal(): BelongsTo
    {
        return $this->belongsTo(Meal::class, 'wednesday_meal_id');
    }

    public function thursdayMeal(): BelongsTo
    {
        return $this->belongsTo(Meal::class, 'thursday_meal_id');
    }

    public function fridayMeal(): BelongsTo
    {
        return $this->belongsTo(Meal::class, 'friday_meal_id');
    }

    public function mealForDate(\Carbon\Carbon $date): ?Meal
    {
        $day = strtolower($date->englishDayOfWeek());

        return match ($day) {
            'monday' => $this->mondayMeal,
            'tuesday' => $this->tuesdayMeal,
            'wednesday' => $this->wednesdayMeal,
            'thursday' => $this->thursdayMeal,
            'friday' => $this->fridayMeal,
            default => null,
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($type) {
            if (empty($type->slug)) {
                $type->slug = \Illuminate\Support\Str::slug($type->name);
            }
        });

        static::updating(function ($type) {
            if ($type->isDirty('name') && empty($type->slug)) {
                $type->slug = \Illuminate\Support\Str::slug($type->name);
            }
        });
    }
}
