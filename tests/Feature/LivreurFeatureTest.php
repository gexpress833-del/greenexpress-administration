<?php

namespace Tests\Feature;

use App\Models\Delivery;
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

    public function test_livreur_validate_by_code_generates_agent_commission(): void
    {
        $livreur = $this->livreur();
        $order = Order::factory()->create([
            'client_validation_code' => 'ABC123',
            'status' => 'delivered',
        ]);
        $delivery = Delivery::factory()->create([
            'livreur_id' => $livreur->id,
            'order_id' => $order->id,
            'status' => 'delivered',
        ]);

        $this->assertDatabaseMissing('commissions', ['order_id' => $order->id]);

        $response = $this->actingAs($livreur)
            ->post(route('livreur.deliveries.validate-by-code', $delivery), [
                'validation_code' => 'ABC123',
            ]);

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertNotNull($order->client_validated_at);
        $this->assertDatabaseHas('commissions', ['order_id' => $order->id]);
    }

    public function test_livreur_validate_by_code_rejects_wrong_code(): void
    {
        $livreur = $this->livreur();
        $order = Order::factory()->create([
            'client_validation_code' => 'ABC123',
            'status' => 'delivered',
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
        $this->assertDatabaseMissing('commissions', ['order_id' => $order->id]);
    }

    public function test_livreur_deliver_does_not_generate_commission(): void
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

        $this->assertDatabaseMissing('commissions', ['order_id' => $order->id]);
    }

    public function test_livreur_qr_form_with_valid_code_redirects_to_show(): void
    {
        $livreur = $this->livreur();
        $order = Order::factory()->create([
            'client_validation_code' => 'QRTEST',
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
}
