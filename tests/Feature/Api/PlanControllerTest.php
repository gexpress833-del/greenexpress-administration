<?php

namespace Tests\Feature\Api;

use App\Models\SubscriptionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_plans_index_returns_active_plans(): void
    {
        SubscriptionType::factory()->create(['name' => 'Plan 1', 'is_active' => true]);
        SubscriptionType::factory()->create(['name' => 'Plan 2', 'is_active' => true]);
        SubscriptionType::factory()->create(['name' => 'Inactive Plan', 'is_active' => false]);

        $response = $this->getJson('/api/plans');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'description',
                    'type',
                    'meals_per_day',
                    'price',
                    'price_fc',
                    'duration_days',
                    'total_deliveries',
                ],
            ]);
    }

    public function test_plans_index_returns_empty_when_no_active_plans(): void
    {
        SubscriptionType::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/plans');

        $response->assertStatus(200)
            ->assertJsonCount(0);
    }
}
