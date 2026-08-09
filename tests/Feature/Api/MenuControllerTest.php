<?php

namespace Tests\Feature\Api;

use App\Models\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_menus_index_returns_active_meals(): void
    {
        Meal::factory()->create(['name' => 'Meal 1', 'is_active' => true]);
        Meal::factory()->create(['name' => 'Meal 2', 'is_active' => true]);
        Meal::factory()->create(['name' => 'Inactive Meal', 'is_active' => false]);

        $response = $this->getJson('/api/menus');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'description',
                    'category',
                    'price',
                    'price_fc',
                    'image',
                    'is_available',
                ],
            ]);
    }

    public function test_menus_index_returns_empty_when_no_active_meals(): void
    {
        Meal::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/menus');

        $response->assertStatus(200)
            ->assertJsonCount(0);
    }
}
