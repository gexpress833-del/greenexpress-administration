<?php

namespace Tests\Feature;

use App\Jobs\SendDailyDeliveryCodes;
use App\Models\Delivery;
use App\Models\DeliveryPoint;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivreurFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function livreur(): User
    {
        return User::factory()->livreur()->create();
    }

    public function test_livreur_can_view_deliveries_index(): void
    {
        $livreur = $this->livreur();
        Delivery::factory()->count(2)->create(['livreur_id' => $livreur->id]);

        $this->actingAs($livreur)
            ->get(route('livreur.deliveries.index'))
            ->assertStatus(200);
    }

    public function test_livreur_can_view_delivery(): void
    {
        $livreur = $this->livreur();
        $delivery = Delivery::factory()->create(['livreur_id' => $livreur->id]);

        $this->actingAs($livreur)
            ->get(route('livreur.deliveries.show', $delivery))
            ->assertStatus(200);
    }

    public function test_livreur_can_mark_delivery_as_delivered(): void
    {
        $livreur = $this->livreur();
        $order = Order::factory()->create();
        $delivery = Delivery::factory()->create([
            'livreur_id' => $livreur->id,
            'order_id' => $order->id,
            'status' => 'assigned',
        ]);

        $this->actingAs($livreur)
            ->post(route('livreur.deliveries.deliver', $delivery))
            ->assertRedirect(route('livreur.deliveries.index'));

        $delivery->refresh();
        $order->refresh();
        $this->assertEquals('delivered', $delivery->status);
        $this->assertNotNull($delivery->delivered_at);
        $this->assertEquals('delivered', $order->status);
    }

    public function test_non_livreur_cannot_access_livreur_routes(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client)
            ->get(route('livreur.deliveries.index'))
            ->assertForbidden();
    }

    public function test_livreur_cannot_deliver_another_livreurs_delivery(): void
    {
        $livreur = $this->livreur();
        $other = $this->livreur();
        $delivery = Delivery::factory()->create(['livreur_id' => $other->id]);

        $this->actingAs($livreur)
            ->post(route('livreur.deliveries.deliver', $delivery))
            ->assertForbidden();

        $this->assertNotEquals('delivered', $delivery->fresh()->status);
    }

    public function test_livreur_can_notify_client_via_whatsapp(): void
    {
        $livreur = $this->livreur();
        $order = Order::factory()->create(['client_phone' => '+243999999999']);
        $delivery = Delivery::factory()->create([
            'livreur_id' => $livreur->id,
            'order_id' => $order->id,
        ]);

        $response = $this->actingAs($livreur)
            ->post(route('livreur.deliveries.notify', $delivery));

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('whatsapp_link');
        $this->assertStringStartsWith('https://wa.me/243999999999?text=', session('whatsapp_link'));
    }

    public function test_livreur_cannot_notify_for_another_livreurs_delivery(): void
    {
        $livreur = $this->livreur();
        $other = $this->livreur();
        $delivery = Delivery::factory()->create(['livreur_id' => $other->id]);

        $this->actingAs($livreur)
            ->post(route('livreur.deliveries.notify', $delivery))
            ->assertForbidden();
    }

    public function test_livreur_validate_by_code_generates_agent_points(): void
    {
        $livreur = $this->livreur();
        $order = Order::factory()->create([
            'client_validation_code' => 'ABC123',
            'status' => 'delivered',
            'admin_validated_at' => now(),
        ]);
        $delivery = Delivery::factory()->create([
            'livreur_id' => $livreur->id,
            'order_id' => $order->id,
            'status' => 'delivered',
        ]);

        $this->assertDatabaseMissing('agent_points', ['order_id' => $order->id]);

        $response = $this->actingAs($livreur)
            ->post(route('livreur.deliveries.validate-by-code', $delivery), [
                'validation_code' => 'ABC123',
            ]);

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('reward');

        $order->refresh();
        $this->assertNotNull($order->client_validated_at);
        $this->assertDatabaseHas('agent_points', ['order_id' => $order->id]);
    }

    public function test_validation_code_form_visible_after_deliver_before_client_validation(): void
    {
        $livreur = $this->livreur();
        $order = Order::factory()->create([
            'client_validation_code' => 'ABC123',
            'status' => 'delivered',
            'admin_validated_at' => now(),
            'client_validated_at' => null,
        ]);
        $delivery = Delivery::factory()->create([
            'livreur_id' => $livreur->id,
            'order_id' => $order->id,
            'status' => 'delivered',
        ]);

        $response = $this->actingAs($livreur)
            ->get(route('livreur.deliveries.show', $delivery));

        $response->assertStatus(200);
        $response->assertSee('Valider la livraison par code');
        $response->assertDontSee($order->client_validation_code);
    }

    public function test_validation_code_form_hidden_after_client_validation(): void
    {
        $livreur = $this->livreur();
        $order = Order::factory()->create([
            'client_validation_code' => 'ABC123',
            'status' => 'delivered',
            'admin_validated_at' => now(),
            'client_validated_at' => now(),
        ]);
        $delivery = Delivery::factory()->create([
            'livreur_id' => $livreur->id,
            'order_id' => $order->id,
            'status' => 'delivered',
        ]);

        $response = $this->actingAs($livreur)
            ->get(route('livreur.deliveries.show', $delivery));

        $response->assertStatus(200);
        $response->assertDontSee('Valider la livraison par code');
        $response->assertSee('Livraison validée par le client');
        $response->assertSee($order->client_validation_code);
    }

    public function test_livreur_validate_by_code_rejects_wrong_code(): void
    {
        $livreur = $this->livreur();
        $order = Order::factory()->create([
            'client_validation_code' => 'ABC123',
            'status' => 'delivered',
            'admin_validated_at' => now(),
        ]);
        $delivery = Delivery::factory()->create([
            'livreur_id' => $livreur->id,
            'order_id' => $order->id,
            'status' => 'delivered',
        ]);

        $response = $this->actingAs($livreur)
            ->post(route('livreur.deliveries.validate-by-code', $delivery), [
                'validation_code' => 'WRONG1',
            ]);

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('error');
    }

    public function test_livreur_deliver_does_not_generate_points(): void
    {
        $livreur = $this->livreur();
        $order = Order::factory()->create();
        $delivery = Delivery::factory()->create([
            'livreur_id' => $livreur->id,
            'order_id' => $order->id,
        ]);

        $this->actingAs($livreur)
            ->post(route('livreur.deliveries.deliver', $delivery))
            ->assertRedirect(route('livreur.deliveries.index'));

    }

    public function test_livreur_qr_form_with_valid_code_redirects_to_show(): void
    {
        $livreur = $this->livreur();
        $order = Order::factory()->create([
            'client_validation_code' => 'QRTEST',
            'status' => 'confirmed',
            'admin_validated_at' => now(),
        ]);
        $delivery = Delivery::factory()->create([
            'livreur_id' => $livreur->id,
            'order_id' => $order->id,
        ]);

        $response = $this->actingAs($livreur)
            ->get(route('livreur.deliveries.validate-qr-form', [
                'order_id' => $order->id,
                'code' => 'QRTEST',
            ]));

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('validation_code', 'QRTEST');
    }

    public function test_livreur_qr_form_with_invalid_code_shows_error(): void
    {
        $livreur = $this->livreur();
        $order = Order::factory()->create([
            'client_validation_code' => 'QRTEST',
            'status' => 'confirmed',
            'admin_validated_at' => now(),
        ]);
        $delivery = Delivery::factory()->create([
            'livreur_id' => $livreur->id,
            'order_id' => $order->id,
        ]);

        $response = $this->actingAs($livreur)
            ->get(route('livreur.deliveries.validate-qr-form', [
                'order_id' => $order->id,
                'code' => 'BADBAD',
            ]));

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('error');
    }

    public function test_livreur_can_view_points_page_with_earned_points(): void
    {
        $livreur = $this->livreur();
        $delivery = Delivery::factory()->create(['livreur_id' => $livreur->id]);
        DeliveryPoint::create([
            'delivery_id' => $delivery->id,
            'livreur_id' => $livreur->id,
            'points' => 7,
            'description' => 'Points gagnés pour livraison validée par le client',
        ]);

        $response = $this->actingAs($livreur)->get(route('livreur.points.index'));

        $response->assertStatus(200);
        $response->assertSee('7');
    }

    public function test_daily_delivery_code_job_notifies_each_order_only_once(): void
    {
        $client = User::factory()->client()->create();
        $order = Order::factory()->create([
            'client_id' => $client->id,
            'delivery_date' => today(),
            'status' => 'confirmed',
            'client_validation_code' => 'ABC123',
        ]);

        app(SendDailyDeliveryCodes::class)->handle();
        app(SendDailyDeliveryCodes::class)->handle();

        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $client->id,
            'notifiable_type' => User::class,
        ]);
        $this->assertSame($order->id, $client->notifications()->first()->data['order_id']);
    }

    public function test_livreur_points_page_shows_zero_when_no_points(): void
    {
        $livreur = $this->livreur();

        $response = $this->actingAs($livreur)->get(route('livreur.points.index'));

        $response->assertStatus(200);
        $response->assertSee('Aucun point gagné pour le moment.');
    }
}
