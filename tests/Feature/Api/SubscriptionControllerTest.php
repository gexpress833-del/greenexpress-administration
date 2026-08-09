<?php

namespace Tests\Feature\Api;

use App\Models\Subscription;
use App\Models\SubscriptionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_store_creates_subscription(): void
    {
        $user = User::factory()->create();
        $subscriptionType = SubscriptionType::factory()->create([
            'duration_days' => 30,
            'total_deliveries' => 10,
            'price' => 100.00,
            'price_fc' => 200000.00,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/subscriptions', [
                'subscription_type_id' => $subscriptionType->id,
                'delivery_address' => '123 Test Street',
                'phone' => '+1234567890',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'status',
                    'start_date',
                    'end_date',
                    'remaining_deliveries',
                ],
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'subscription_type_id' => $subscriptionType->id,
            'status' => 'pending',
        ]);
    }

    public function test_subscription_renew_renews_subscription(): void
    {
        $user = User::factory()->create();
        $subscriptionType = SubscriptionType::factory()->create([
            'duration_days' => 30,
            'total_deliveries' => 10,
            'price' => 100.00,
            'price_fc' => 200000.00,
        ]);

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_type_id' => $subscriptionType->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/subscriptions/{$subscription->id}/renew");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'status',
                    'start_date',
                    'end_date',
                    'remaining_deliveries',
                ],
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'active',
            'remaining_deliveries' => 10,
        ]);
    }

    public function test_subscription_store_requires_authentication(): void
    {
        $subscriptionType = SubscriptionType::factory()->create();

        $response = $this->postJson('/api/subscriptions', [
            'subscription_type_id' => $subscriptionType->id,
            'delivery_address' => '123 Test Street',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(401);
    }
}
