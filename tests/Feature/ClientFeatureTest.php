<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function client(): User
    {
        return User::factory()->client()->create();
    }

    public function test_client_can_view_dashboard(): void
    {
        $client = $this->client();

        $this->actingAs($client)
            ->get(route('client.dashboard'))
            ->assertStatus(200);
    }

    public function test_client_can_view_subscriptions_index_and_show(): void
    {
        $client = $this->client();
        $sub = Subscription::factory()->active()->create(['client_id' => $client->id]);

        $this->actingAs($client)->get(route('client.subscriptions.index'))->assertStatus(200);
        $this->actingAs($client)->get(route('client.subscriptions.show', $sub))->assertStatus(200);
    }

    public function test_client_can_renew_subscription(): void
    {
        $client = $this->client();
        $sub = Subscription::factory()->active()->create([
            'client_id' => $client->id,
            'type' => 'weekly',
            'total_days' => 7,
            'remaining_days' => 2,
        ]);

        $this->actingAs($client)->post(route('client.subscriptions.renew', $sub), [
            'type' => 'monthly',
        ])->assertRedirect(route('client.subscriptions.index'));

        $sub->refresh();
        $this->assertEquals('active', $sub->status);
        $this->assertEquals('weekly', $sub->type);
        $this->assertEquals(7, $sub->total_days);

        $newSub = Subscription::where('client_id', $client->id)
            ->where('id', '!=', $sub->id)
            ->latest()
            ->first();

        $this->assertNotNull($newSub);
        $this->assertEquals('monthly', $newSub->type);
        $this->assertEquals('pending', $newSub->status);
        $this->assertEquals(30, $newSub->total_days);
        $this->assertNull($newSub->admin_validated_at);
        $this->assertNull($newSub->validated_by);
        $this->assertEquals($sub->agent_id, $newSub->agent_id);
    }

    public function test_client_can_request_suspension(): void
    {
        $client = $this->client();
        $sub = Subscription::factory()->active()->create(['client_id' => $client->id]);

        $this->actingAs($client)->post(route('client.subscriptions.suspend', $sub), [
            'reason' => 'Voyage',
            'duration_days' => 5,
        ])->assertRedirect(route('client.subscriptions.index'));

        $this->assertDatabaseHas('subscription_suspensions', [
            'subscription_id' => $sub->id,
            'reason' => 'Voyage',
            'status' => 'pending',
        ]);
    }

    public function test_client_can_reactivate_suspended_subscription(): void
    {
        $client = $this->client();
        $sub = Subscription::factory()->suspended()->create(['client_id' => $client->id]);

        $this->actingAs($client)->post(route('client.subscriptions.reactivate', $sub))
            ->assertRedirect(route('client.subscriptions.index'));

        $sub->refresh();
        $this->assertEquals('pending', $sub->status);
        $this->assertNull($sub->admin_validated_at);
        $this->assertNull($sub->validated_by);
    }

    public function test_client_can_view_orders(): void
    {
        $client = $this->client();
        $order = Order::factory()->create(['client_id' => $client->id]);

        $this->actingAs($client)->get(route('client.orders.index'))->assertStatus(200);
        $this->actingAs($client)->get(route('client.orders.show', $order))->assertStatus(200);
    }

    public function test_client_can_view_deliveries(): void
    {
        $client = $this->client();
        $order = Order::factory()->create(['client_id' => $client->id]);
        $delivery = Delivery::factory()->create(['order_id' => $order->id]);

        $this->actingAs($client)->get(route('client.deliveries.index'))->assertStatus(200);
        $this->actingAs($client)->get(route('client.deliveries.show', $delivery))->assertStatus(200);
    }

    public function test_non_client_cannot_access_client_routes(): void
    {
        $agent = User::factory()->agent()->create();

        $this->actingAs($agent)
            ->get(route('client.dashboard'))
            ->assertForbidden();
    }

    public function test_client_cannot_view_another_clients_subscription(): void
    {
        $client = $this->client();
        $other = $this->client();
        $sub = Subscription::factory()->active()->create(['client_id' => $other->id]);

        $this->actingAs($client)
            ->get(route('client.subscriptions.show', $sub))
            ->assertForbidden();
    }

    public function test_client_cannot_renew_another_clients_subscription(): void
    {
        $client = $this->client();
        $other = $this->client();
        $sub = Subscription::factory()->active()->create(['client_id' => $other->id]);

        $this->actingAs($client)
            ->post(route('client.subscriptions.renew', $sub), ['type' => 'weekly'])
            ->assertForbidden();
    }

    public function test_client_cannot_view_another_clients_order(): void
    {
        $client = $this->client();
        $other = $this->client();
        $order = Order::factory()->create(['client_id' => $other->id]);

        $this->actingAs($client)
            ->get(route('client.orders.show', $order))
            ->assertForbidden();
    }
}
