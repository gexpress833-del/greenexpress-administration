<?php

namespace Database\Factories;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Delivery>
 */
class DeliveryFactory extends Factory
{
    protected $model = Delivery::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory()->confirmed(),
            'livreur_id' => User::factory()->livreur(),
            'delivery_code' => 'DLV-'.strtoupper(Str::random(8)),
            'status' => 'assigned',
            'picked_up_at' => null,
            'delivered_at' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'picked_up_at' => now()->subHour(),
            'delivered_at' => now(),
        ]);
    }
}
