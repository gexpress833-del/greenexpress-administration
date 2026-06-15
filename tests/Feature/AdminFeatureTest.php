<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Commission;
use App\Models\Meal;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\SubscriptionSuspension;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_admin_can_validate_subscription(): void
    {
        $admin = $this->admin();
        $client = User::factory()->client()->create(['phone' => '+243777777777']);
        $sub = Subscription::factory()->create(['status' => 'pending', 'client_id' => $client->id]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.subscriptions.update', $sub), ['status' => 'active']);

        $response->assertRedirect(route('admin.subscriptions.show', $sub));
        $response->assertSessionHas('whatsapp_link');
        $this->assertStringStartsWith('https://wa.me/243777777777?text=', session('whatsapp_link'));

        $sub->refresh();
        $this->assertEquals('active', $sub->status);
        $this->assertEquals($admin->id, $sub->validated_by);
        $this->assertNotNull($sub->admin_validated_at);
    }

    public function test_admin_can_approve_withdrawal(): void
    {
        $admin = $this->admin();
        $agent = \App\Models\User::factory()->agent()->create(['phone' => '+243888888888']);
        $withdrawal = Withdrawal::factory()->create(['status' => 'pending', 'agent_id' => $agent->id]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.withdrawals.update', $withdrawal), ['status' => 'approved']);

        $response->assertRedirect(route('admin.withdrawals.index'));
        $response->assertSessionHas('whatsapp_link');
        $this->assertStringStartsWith('https://wa.me/243888888888?text=', session('whatsapp_link'));

        $withdrawal->refresh();
        $this->assertEquals('approved', $withdrawal->status);
        $this->assertEquals($admin->id, $withdrawal->processed_by);
    }

    public function test_admin_can_reject_withdrawal(): void
    {
        $admin = $this->admin();
        $withdrawal = Withdrawal::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->patch(route('admin.withdrawals.update', $withdrawal), ['status' => 'rejected'])
            ->assertRedirect(route('admin.withdrawals.index'));

        $this->assertEquals('rejected', $withdrawal->fresh()->status);
    }

    public function test_admin_withdrawal_update_rejects_invalid_status(): void
    {
        $admin = $this->admin();
        $withdrawal = Withdrawal::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->patch(route('admin.withdrawals.update', $withdrawal), ['status' => 'bogus'])
            ->assertSessionHasErrors('status');
    }

    public function test_admin_can_assign_delivery(): void
    {
        $admin = $this->admin();
        $order = Order::factory()->confirmed()->create();
        $livreur = User::factory()->livreur()->create();

        $this->actingAs($admin)
            ->post(route('admin.deliveries.store'), [
                'order_id' => $order->id,
                'livreur_id' => $livreur->id,
            ])
            ->assertRedirect(route('admin.deliveries.index'));

        $this->assertDatabaseHas('deliveries', [
            'order_id' => $order->id,
            'livreur_id' => $livreur->id,
        ]);
        $this->assertEquals('delivering', $order->fresh()->status);
    }

    public function test_admin_can_accept_suspension(): void
    {
        $admin = $this->admin();
        $sub = Subscription::factory()->active()->create();
        $suspension = SubscriptionSuspension::factory()->create([
            'subscription_id' => $sub->id,
            'status' => 'pending',
            'duration_days' => 5,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.suspensions.accept', $suspension))
            ->assertRedirect(route('admin.suspensions.index'));

        $this->assertEquals('accepted', $suspension->fresh()->status);
        $this->assertEquals('suspended', $sub->fresh()->status);
    }

    public function test_admin_can_reject_suspension(): void
    {
        $admin = $this->admin();
        $suspension = SubscriptionSuspension::factory()->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->post(route('admin.suspensions.reject', $suspension))
            ->assertRedirect(route('admin.suspensions.index'));

        $this->assertEquals('rejected', $suspension->fresh()->status);
    }

    public function test_admin_can_create_user(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Nouveau Agent',
            'email' => 'new.agent@example.com',
            'role' => 'agent',
            'password' => 'password123',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'new.agent@example.com', 'role' => 'agent']);
    }

    public function test_admin_can_update_and_delete_user(): void
    {
        $admin = $this->admin();
        $user = User::factory()->client()->create();

        $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'Renamed',
            'email' => $user->email,
            'role' => 'livreur',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertEquals('livreur', $user->fresh()->role);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_admin_can_create_category_and_meal(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Plats chauds',
            'slug' => 'plats-chauds',
            'is_active' => true,
        ])->assertRedirect(route('admin.categories.index'));

        $category = Category::where('slug', 'plats-chauds')->first();
        $this->assertNotNull($category);

        $this->actingAs($admin)->post(route('admin.meals.store'), [
            'name' => 'Poulet rôti',
            'price' => 12.5,
            'category_id' => $category->id,
            'status' => 'available',
            'is_active' => true,
        ])->assertRedirect(route('admin.meals.index'));

        $this->assertDatabaseHas('meals', ['name' => 'Poulet rôti']);
    }

    public function test_admin_can_view_commissions_index(): void
    {
        $admin = $this->admin();
        Commission::factory()->count(2)->create();

        $this->actingAs($admin)->get(route('admin.commissions.index'))->assertStatus(200);
    }

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $agent = User::factory()->agent()->create();

        $this->actingAs($agent)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }
}
