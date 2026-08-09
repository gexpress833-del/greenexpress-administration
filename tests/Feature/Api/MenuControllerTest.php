<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_menus_index_returns_active_meals(): void
    {
        $category = Category::factory()->create();
        Meal::factory()->create(['name' => 'Meal 1', 'is_active' => true, 'category_id' => $category->id]);
        Meal::factory()->create(['name' => 'Meal 2', 'is_active' => true, 'category_id' => $category->id]);
        Meal::factory()->create(['name' => 'Inactive Meal', 'is_active' => false]);

        $response = $this->getJson('/api/menus');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'category',
                        'category_id',
                        'price',
                        'price_fc',
                        'image',
                        'is_available',
                    ],
                ],
            ]);
    }

    public function test_menus_index_returns_empty_when_no_active_meals(): void
    {
        Meal::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/menus');

        $response->assertStatus(200)
            ->assertJson(['data' => []]);
    }
}
