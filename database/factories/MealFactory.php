<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Meal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meal>
 */
class MealFactory extends Factory
{
    protected $model = Meal::class;

    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->words(2, true)),
            'description' => fake()->optional()->sentence(),
            'image' => null,
            'price' => fake()->randomFloat(2, 1, 50),
            'category_id' => Category::factory(),
            'status' => 'available',
            'is_active' => true,
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'unavailable']);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
