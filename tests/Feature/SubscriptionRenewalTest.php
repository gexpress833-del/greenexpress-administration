<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PointService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionRenewalTest extends TestCase
{
    use RefreshDatabase;

    public function test_renewal_creates_new_subscription_and_preserves_old_one(): void
    {
        $client = User::factory()->client()->create();
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
        $this->assertEquals($sub->agent_id, $newSub->agent_id);
    }

    public function test_admin_validation_of_renewal_defers_start_date_when_active_subscription_exists(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        $oldSub = Subscription::factory()->active()->create([
            'client_id' => $client->id,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(15)->format('Y-m-d'),
            'type' => 'monthly',
            'total_days' => 30,
        ]);

        $newSub = Subscription::factory()->create([
            'client_id' => $client->id,
            'agent_id' => $oldSub->agent_id,
            'type' => 'monthly',
            'total_days' => 30,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.subscriptions.update', $newSub), ['status' => 'active'])
            ->assertRedirect(route('admin.subscriptions.show', $newSub));

        $newSub->refresh();
        $this->assertEquals('active', $newSub->status);

        $expectedStart = now()->addDays(15)->addDay()->startOfDay();
        $this->assertEquals(
            $expectedStart->format('Y-m-d'),
            $newSub->start_date->format('Y-m-d'),
            'New subscription start_date should be the day after the old subscription end_date'
        );
    }

    public function test_admin_validation_of_renewal_uses_today_when_no_active_subscription(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        $oldSub = Subscription::factory()->create([
            'client_id' => $client->id,
            'status' => 'expired',
            'end_date' => now()->subDays(5)->format('Y-m-d'),
            'type' => 'monthly',
            'total_days' => 30,
        ]);

        $newSub = Subscription::factory()->create([
            'client_id' => $client->id,
            'agent_id' => $oldSub->agent_id,
            'type' => 'monthly',
            'total_days' => 30,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.subscriptions.update', $newSub), ['status' => 'active'])
            ->assertRedirect(route('admin.subscriptions.show', $newSub));

        $newSub->refresh();
        $this->assertEquals('active', $newSub->status);
        $this->assertEquals(
            now()->format('Y-m-d'),
            $newSub->start_date->format('Y-m-d'),
            'New subscription start_date should be today when no active subscription exists'
        );
    }

    public function test_agent_receives_points_on_renewal_validation(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        $oldSub = Subscription::factory()->active()->create([
            'client_id' => $client->id,
            'end_date' => now()->addDays(15)->format('Y-m-d'),
        ]);

        $newSub = Subscription::factory()->create([
            'client_id' => $client->id,
            'agent_id' => $oldSub->agent_id,
            'type' => 'monthly',
            'total_days' => 30,
            'status' => 'pending',
        ]);

        $this->assertDatabaseMissing('agent_points', ['subscription_id' => $newSub->id]);

        $this->actingAs($admin)
            ->patch(route('admin.subscriptions.update', $newSub), ['status' => 'active']);

        $this->assertDatabaseMissing('agent_points', [
            'subscription_id' => $newSub->id,
            'agent_id' => $oldSub->agent_id,
        ]);
    }

    public function test_agent_receives_points_on_first_subscription_validation(): void
    {
        $admin = User::factory()->admin()->create();
        $sub = Subscription::factory()->create([
            'status' => 'pending',
            'type' => 'monthly',
            'total_days' => 30,
        ]);

        $this->assertDatabaseMissing('agent_points', ['subscription_id' => $sub->id]);

        $this->actingAs($admin)
            ->patch(route('admin.subscriptions.update', $sub), ['status' => 'active']);

        $this->assertDatabaseMissing('agent_points', [
            'subscription_id' => $sub->id,
            'agent_id' => $sub->agent_id,
        ]);
    }

    public function test_agent_receives_fixed_points_for_validated_subscription_orders(): void
    {
        $agent = User::factory()->agent()->create();
        $client = User::factory()->client()->create();

        $sub = Subscription::factory()->active()->create([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
        ]);

        $order = Order::factory()->create([
            'agent_id' => $agent->id,
            'client_id' => $client->id,
            'subscription_id' => $sub->id,
            'status' => 'delivered',
            'client_validated_at' => now(),
            'total_amount_fc' => 10000,
        ]);

        $result = app(PointService::class)->creditForOrder($order);

        $this->assertNotNull($result);
        $this->assertDatabaseHas('agent_points', [
            'order_id' => $order->id,
            'agent_id' => $agent->id,
            'points' => 12,
        ]);
    }
}
