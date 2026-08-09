<?php

namespace Tests\Feature\Api;

use App\Models\SubscriptionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_quote_store_creates_quote(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/quotes', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '+1234567890',
                'message' => 'I am interested in your services',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('quotes', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_quote_store_with_subscription_type(): void
    {
        $user = User::factory()->create();
        $subscriptionType = SubscriptionType::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/quotes', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '+1234567890',
                'message' => 'I am interested in your services',
                'subscription_type_id' => $subscriptionType->id,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('quotes', [
            'name' => 'John Doe',
            'subscription_type_id' => $subscriptionType->id,
        ]);
    }

    public function test_quote_store_requires_authentication(): void
    {
        $response = $this->postJson('/api/quotes', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'message' => 'I am interested in your services',
        ]);

        $response->assertStatus(401);
    }

    public function test_quote_store_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/quotes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'phone', 'message']);
    }
}
