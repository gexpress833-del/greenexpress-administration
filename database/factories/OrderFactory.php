<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'code' => 'GX-'.strtoupper(Str::random(8)),
            'client_validation_code' => strtoupper(Str::random(6)),
            'agent_id' => User::factory()->agent(),
            'client_id' => User::factory()->client(),
            'client_name' => fake()->name(),
            'client_phone' => fake()->phoneNumber(),
            'delivery_address' => fake()->address(),
            'delivery_date' => fake()->dateTimeBetween('now', '+10 days')->format('Y-m-d'),
            'total_amount' => fake()->randomFloat(2, 5, 200),
            'status' => 'pending',
            'notes' => fake()->optional()->sentence(),
            'confirmed_at' => null,
            'delivered_at' => null,
            'client_validated_at' => null,
        ];
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'admin_validated_at' => now(),
        ]);
    }
}
