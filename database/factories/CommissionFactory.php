<?php

namespace Database\Factories;

use App\Models\Commission;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Commission>
 */
class CommissionFactory extends Factory
{
    protected $model = Commission::class;

    public function definition(): array
    {
        $usd = fake()->randomFloat(2, 0.5, 20);

        return [
            'agent_id' => User::factory()->agent(),
            'order_id' => Order::factory(),
            'type' => 'points',
            'points' => fake()->numberBetween(0, 50),
            'amount_usd' => $usd,
            'amount_fc' => round($usd * 2800, 2),
            'description' => fake()->sentence(),
        ];
    }
}
