<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['weekly', 'monthly']);
        $totalDays = $type === 'weekly' ? 7 : 30;
        $startDate = fake()->dateTimeBetween('-10 days', 'now');
        $endDate = (clone $startDate)->modify("+{$totalDays} days");

        return [
            'client_id' => User::factory()->client(),
            'agent_id' => User::factory()->agent(),
            'type' => $type,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_days' => $totalDays,
            'remaining_days' => $totalDays,
            'price' => fake()->randomFloat(2, 20, 200),
            'currency' => 'usd',
            'price_fc' => fake()->randomFloat(2, 50000, 500000),
            'status' => 'pending',
            'admin_validated_at' => null,
            'validated_by' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'admin_validated_at' => now(),
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'suspended']);
    }
}
