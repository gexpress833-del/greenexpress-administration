<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_fc' => 'decimal:2',
        'duration_days' => 'integer',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
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
