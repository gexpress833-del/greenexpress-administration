<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Withdrawal>
 */
class WithdrawalFactory extends Factory
{
    protected $model = Withdrawal::class;

    public function definition(): array
    {
        $usd = fake()->randomFloat(2, 1, 100);

        return [
            'agent_id' => User::factory()->agent(),
            'amount_usd' => $usd,
            'amount_fc' => round($usd * 2800, 2),
            'status' => 'pending',
            'notes' => null,
            'processed_by' => null,
            'processed_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'processed_at' => now(),
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'processed_at' => now(),
        ]);
    }
}
