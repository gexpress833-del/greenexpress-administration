<?php

namespace Database\Factories;

use App\Models\SubscriptionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionType>
 */
class SubscriptionTypeFactory extends Factory
{
    protected $model = SubscriptionType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'slug' => fake()->slug(),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 20, 200),
            'price_fc' => fake()->randomFloat(2, 50000, 500000),
            'duration_days' => fake()->randomElement([7, 14, 30]),
            'meals_per_day' => fake()->randomElement([1, 2]),
            'currency' => 'usd',
            'is_active' => true,
            'display_order' => 0,
        ];
    }
}
