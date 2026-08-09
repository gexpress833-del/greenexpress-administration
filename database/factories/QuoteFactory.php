<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\SubscriptionType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteFactory extends Factory
{
    protected $model = Quote::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'phone' => fake()->phoneNumber(),
            'message' => fake()->paragraph(),
            'subscription_type_id' => SubscriptionType::inRandomOrder()->first()?->id,
            'user_id' => User::inRandomOrder()->first()?->id,
            'status' => 'pending',
        ];
    }
}
