<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\SubscriptionSuspension;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionSuspension>
 */
class SubscriptionSuspensionFactory extends Factory
{
    protected $model = SubscriptionSuspension::class;

    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'status' => 'pending',
            'reason' => fake()->sentence(),
            'duration_days' => fake()->numberBetween(1, 30),
            'suspension_start' => null,
            'suspension_end' => null,
            'admin_notes' => null,
            'processed_by' => null,
            'processed_at' => null,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
            'suspension_start' => now()->format('Y-m-d'),
            'suspension_end' => now()->addDays($attributes['duration_days'] ?? 7)->format('Y-m-d'),
            'processed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'processed_at' => now(),
        ]);
    }
}
