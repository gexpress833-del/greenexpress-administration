<?php

namespace Tests\Feature;

use App\Models\Commission;
use App\Models\Meal;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AgentFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function agent(): User
    {
        return User::factory()->agent()->create();
    }

    public function test_agent_can_view_orders_index(): void
    {
        $agent = $this->agent();
        Order::factory()->count(3)->create(['agent_id' => $agent->id]);

        $this->actingAs($agent)
            ->get(route('agent.orders.index'))
            ->assertStatus(200);
    }

    public function test_agent_can_view_order_create_form(): void
    {
        $agent = $this->agent();
        Meal::factory()->count(2)->create();

        $this->actingAs($agent)
            ->get(route('agent.orders.create'))
            ->assertStatus(200);
    }

    public function test_agent_can_create_order_with_items(): void
    {
        $agent = $this->agent();
        $meal = Meal::factory()->create(['price' => 10]);

        $response = $this->actingAs($agent)->post(route('agent.orders.store'), [
            'client_name' => 'Jean Test',
            'client_phone' => '+243000000000',
            'delivery_address' => 'Kolwezi',
            'delivery_date' => now()->addDay()->format('Y-m-d'),
            'currency' => 'usd',
            'items' => [
                ['meal_id' => $meal->id, 'quantity' => 3],
            ],
        ]);

        $response->assertRedirect(route('agent.orders.index'));
        $response->assertSessionHas('whatsapp_link');

        $order = Order::where('agent_id', $agent->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(30, (float) $order->total_amount);
        $this->assertCount(1, $order->items);
        $this->assertNotNull($order->client_validation_code);
        $this->assertDatabaseMissing('commissions', ['order_id' => $order->id]);
    }

    public function test_agent_order_store_requires_items(): void
    {
        $agent = $this->agent();

        $this->actingAs($agent)->post(route('agent.orders.store'), [
            'client_name' => 'Jean Test',
            'client_phone' => '+243000000000',
            'delivery_address' => 'Kolwezi',
            'delivery_date' => now()->addDay()->format('Y-m-d'),
            'currency' => 'usd',
        ])->assertSessionHasErrors('items');
    }

    public function test_agent_can_view_their_order(): void
    {
        $agent = $this->agent();
        $order = Order::factory()->create(['agent_id' => $agent->id]);

        $this->actingAs($agent)
            ->get(route('agent.orders.show', $order))
            ->assertStatus(200);
    }

    public function test_agent_can_create_subscription(): void
    {
        $agent = $this->agent();

        $response = $this->actingAs($agent)->post(route('agent.subscriptions.store'), [
            'client_name' => 'Client Sub',
            'client_phone' => '+243111111111',
            'client_email' => 'sub.client@example.com',
            'type' => 'weekly',
            'start_date' => now()->format('Y-m-d'),
            'currency' => 'usd',
            'price' => 50,
            'payment_confirmed' => true,
        ]);

        $response->assertRedirect(route('agent.subscriptions.index'));
        $this->assertDatabaseHas('users', ['email' => 'sub.client@example.com', 'role' => 'client']);
        $this->assertDatabaseHas('subscriptions', ['agent_id' => $agent->id, 'total_days' => 7]);
    }

    public function test_subscription_creation_rolls_back_new_client_when_subscription_fails(): void
    {
        $agent = $this->agent();
        Subscription::creating(function (): void {
            Subscription::flushEventListeners();

            throw new \RuntimeException('Simulated subscription failure');
        });

        $this->actingAs($agent)->post(route('agent.subscriptions.store'), [
            'client_name' => 'Rollback Client',
            'client_phone' => '+243111111112',
            'client_email' => 'rollback.client@example.com',
            'type' => 'weekly',
            'start_date' => now()->format('Y-m-d'),
            'currency' => 'usd',
            'price' => 50,
            'payment_confirmed' => true,
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('users', ['email' => 'rollback.client@example.com']);
        $this->assertDatabaseCount('subscriptions', 0);
    }

    public function test_credential_generation_rolls_back_when_pdf_rendering_fails(): void
    {
        $agent = $this->agent();
        $client = User::factory()->client()->create();
        $originalPassword = $client->password;
        $subscription = Subscription::factory()->active()->create([
            'agent_id' => $agent->id,
            'client_id' => $client->id,
            'client_email' => $client->email,
            'client_phone' => $client->phone,
            'credentials_generated_at' => null,
        ]);
        Pdf::shouldReceive('loadView')->once()->andThrow(new \RuntimeException('PDF failure'));

        $this->actingAs($agent)
            ->post(route('agent.subscriptions.generate-credentials', $subscription))
            ->assertRedirect(route('agent.subscriptions.index'))
            ->assertSessionHas('error');

        $this->assertSame($originalPassword, $client->fresh()->password);
        $this->assertNull($subscription->fresh()->credentials_generated_at);
        $this->assertTrue(Hash::check('password', $client->fresh()->password));
    }

    public function test_agent_subscription_creation_flashes_prefilled_whatsapp_link(): void
    {
        $agent = $this->agent();

        $response = $this->actingAs($agent)->post(route('agent.subscriptions.store'), [
            'client_name' => 'WA Client',
            'client_phone' => '+243 811-111-111',
            'client_email' => 'wa.client@example.com',
            'type' => 'monthly',
            'start_date' => now()->format('Y-m-d'),
            'currency' => 'usd',
            'price' => 80,
            'payment_confirmed' => true,
        ]);

        $response->assertRedirect(route('agent.subscriptions.index'));
        $response->assertSessionHas('whatsapp_link');

        $link = session('whatsapp_link');
        // wa.me avec numéro normalisé (chiffres uniquement) et message pré-rempli encodé.
        $this->assertStringStartsWith('https://wa.me/243811111111?text=', $link);
        $this->assertStringContainsString(rawurlencode('wa.client@example.com'), $link);
    }

    public function test_agent_can_view_commissions_and_withdrawals(): void
    {
        $agent = $this->agent();
        Commission::factory()->create(['agent_id' => $agent->id]);

        $this->actingAs($agent)->get(route('agent.commissions.index'))->assertStatus(200);
        $this->actingAs($agent)->get(route('agent.withdrawals.index'))->assertStatus(200);
    }

    public function test_agent_can_request_withdrawal_within_balance(): void
    {
        $agent = $this->agent();
        Commission::factory()->create([
            'agent_id' => $agent->id,
            'amount_usd' => 20,
            'type' => 'daily_commission',  // Must match getAvailableBalance() filter
        ]);

        $response = $this->actingAs($agent)->post(route('agent.withdrawals.store'), ['amount_usd' => 10]);
        $response->assertStatus(302);

        // Manually check the location header
        $location = $response->headers->get('Location');
        $this->assertEquals(route('agent.withdrawals.index'), $location, "Expected redirect to {$location}");

        $this->assertDatabaseHas('withdrawals', ['agent_id' => $agent->id, 'amount_usd' => 10]);
    }

    public function test_agent_cannot_withdraw_more_than_balance(): void
    {
        $agent = $this->agent();
        Commission::factory()->create([
            'agent_id' => $agent->id,
            'amount_usd' => 5,
            'type' => 'daily_commission',  // Must match getAvailableBalance() filter
        ]);

        $this->actingAs($agent)->post(route('agent.withdrawals.store'), [
            'amount_usd' => 100,
        ])->assertSessionHasErrors('amount_usd');
    }

    public function test_non_agent_cannot_access_agent_routes(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client)
            ->get(route('agent.orders.index'))
            ->assertForbidden();
    }

    public function test_agent_cannot_view_another_agents_order(): void
    {
        $agent = $this->agent();
        $otherAgent = $this->agent();
        $order = Order::factory()->create(['agent_id' => $otherAgent->id]);

        $this->actingAs($agent)
            ->get(route('agent.orders.show', $order))
            ->assertForbidden();
    }
}
