<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
        'meals_per_day',
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
        'meals_per_day' => 'integer',
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

    public function weeklyMenus(): HasMany
    {
        return $this->hasMany(SubscriptionTypeWeeklyMenu::class)->orderBy('week_number')->orderBy('day');
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

    public function mealForDate(Carbon $date, ?Carbon $subscriptionStartDate = null): ?Meal
    {
        $day = strtolower($date->format('l'));

        if (! in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) {
            return null;
        }

        // Defensive fallback: if the weekly menus table doesn't exist yet (migration not run), use legacy menu
        if (! Schema::hasTable('subscription_type_weekly_menus')) {
            return $this->legacyMealForDay($day);
        }

        // Fall back to legacy single-week menu if no start date provided or no weekly menus stored
        if (! $subscriptionStartDate || $this->weeklyMenus->isEmpty()) {
            return $this->legacyMealForDay($day);
        }

        $daysDiff = (int) $subscriptionStartDate->startOfDay()->diffInDays($date->startOfDay(), false);
        if ($daysDiff < 0) {
            return null;
        }

        $weekNumber = (int) floor($daysDiff / 7) + 1;
        $weekNumber = (($weekNumber - 1) % 4) + 1;

        try {
            $menu = $this->weeklyMenus
                ->where('week_number', $weekNumber)
                ->where('day', $day)
                ->first();

            return $menu?->meal;
        } catch (\Throwable $e) {
            Log::warning('Weekly menu lookup failed, falling back to legacy menu', [
                'subscription_type_id' => $this->id,
                'error' => $e->getMessage(),
            ]);

            return $this->legacyMealForDay($day);
        }
    }

    private function legacyMealForDay(string $day): ?Meal
    {
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
                $type->slug = Str::slug($type->name);
            }
        });

        static::updating(function ($type) {
            if ($type->isDirty('name') && empty($type->slug)) {
                $type->slug = Str::slug($type->name);
            }
        });
    }
}
