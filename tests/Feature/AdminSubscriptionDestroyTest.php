<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Subscription;
use App\Models\SubscriptionSuspension;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSubscriptionDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_subscription_with_relations(): void
    {
        $admin = User::factory()->admin()->create();
        $sub = Subscription::factory()->create(['status' => 'active']);

        Order::factory()->create([
            'subscription_id' => $sub->id,
            'agent_id' => $sub->agent_id,
            'client_id' => $sub->client_id,
        ]);

        SubscriptionSuspension::factory()->create([
            'subscription_id' => $sub->id,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.subscriptions.destroy', $sub));

        $response->assertRedirect(route('admin.subscriptions.index'));
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('subscriptions', ['id' => $sub->id]);
    }

    public function test_admin_can_delete_subscription_without_client(): void
    {
        $admin = User::factory()->admin()->create();
        $agent = User::factory()->agent()->create();
        $sub = Subscription::factory()->create([
            'status' => 'pending',
            'agent_id' => $agent->id,
            'client_id' => null,
            'client_name' => 'Test Client',
            'client_phone' => '+243123456789',
            'client_email' => 'test@example.com',
        ]);

        $agent->delete();

        $response = $this->actingAs($admin)
            ->delete(route('admin.subscriptions.destroy', $sub));

        $response->assertRedirect(route('admin.subscriptions.index'));
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('subscriptions', ['id' => $sub->id]);
    }

    public function test_admin_can_delete_subscription_and_view_index_after(): void
    {
        $admin = User::factory()->admin()->create();
        $agent = User::factory()->agent()->create();
        $sub = Subscription::factory()->create([
            'status' => 'pending',
            'agent_id' => $agent->id,
            'client_id' => null,
            'client_name' => 'Orphan Client',
            'client_phone' => '+243999999999',
            'client_email' => 'orphan@example.com',
        ]);

        $agent->delete();

        $this->actingAs($admin)
            ->delete(route('admin.subscriptions.destroy', $sub))
            ->assertRedirect(route('admin.subscriptions.index'));

        $this->actingAs($admin)
            ->get(route('admin.subscriptions.index'))
            ->assertStatus(200);
    }

    public function test_admin_can_reject_subscription(): void
    {
        $admin = User::factory()->admin()->create();
        $sub = Subscription::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)
            ->post(route('admin.subscriptions.reject', $sub));

        $response->assertRedirect(route('admin.subscriptions.index'));
        $response->assertSessionHas('success');

        $sub->refresh();
        $this->assertEquals('rejected', $sub->status);
        $this->assertNotNull($sub->admin_validated_at);
        $this->assertEquals($admin->id, $sub->validated_by);
    }

    public function test_admin_can_reject_subscription_with_deleted_agent(): void
    {
        $admin = User::factory()->admin()->create();
        $agent = User::factory()->agent()->create();
        $sub = Subscription::factory()->create([
            'status' => 'pending',
            'agent_id' => $agent->id,
        ]);

        $agent->delete();

        $response = $this->actingAs($admin)
            ->post(route('admin.subscriptions.reject', $sub));

        $response->assertRedirect(route('admin.subscriptions.index'));
        $response->assertSessionHas('success');

        $this->actingAs($admin)
            ->get(route('admin.subscriptions.index'))
            ->assertStatus(200);
    }
}
