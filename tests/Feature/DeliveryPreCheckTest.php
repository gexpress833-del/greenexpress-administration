<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryPreCheckTest extends TestCase
{
    use RefreshDatabase;

    private function livreur(): User
    {
        return User::factory()->livreur()->create();
    }

    private function createDeliveryWithSubscription(array $subOverrides = [], array $orderOverrides = []): array
    {
        $agent = User::factory()->agent()->create();
        $client = User::factory()->client()->create();

        $sub = Subscription::factory()->active()->create(array_merge([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(20)->format('Y-m-d'),
            'total_days' => 30,
            'remaining_days' => 20,
        ], $subOverrides));

        $order = Order::factory()->confirmed()->create(array_merge([
            'subscription_id' => $sub->id,
            'agent_id' => $agent->id,
            'client_id' => $client->id,
            'delivery_date' => now()->format('Y-m-d'),
            'client_validation_code' => 'ABC123',
            'admin_validated_at' => now(),
        ], $orderOverrides));

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
            'livreur_id' => null,
            'status' => 'pending',
        ]);

        return [$sub, $order, $delivery];
    }

    public function test_delivery_allowed_when_all_conditions_met(): void
    {
        $livreur = $this->livreur();
        [$sub, $order, $delivery] = $this->createDeliveryWithSubscription();

        $delivery->livreur_id = $livreur->id;
        $delivery->save();

        $response = $this->actingAs($livreur)
            ->post(route('livreur.deliveries.validate-by-code', $delivery), [
                'validation_code' => 'ABC123',
            ]);

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('reward');
    }

    public function test_delivery_refused_when_subscription_not_active(): void
    {
        $livreur = $this->livreur();
        [$sub, $order, $delivery] = $this->createDeliveryWithSubscription(['status' => 'suspended']);

        $delivery->livreur_id = $livreur->id;
        $delivery->save();

        $response = $this->actingAs($livreur)
            ->post(route('livreur.deliveries.validate-by-code', $delivery), [
                'validation_code' => 'ABC123',
            ]);

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('error', 'L\'abonnement n\'est pas actif.');
    }

    public function test_delivery_refused_when_date_outside_subscription_period(): void
    {
        $livreur = $this->livreur();
        [$sub, $order, $delivery] = $this->createDeliveryWithSubscription([
            'start_date' => now()->addDays(10)->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $delivery->livreur_id = $livreur->id;
        $delivery->save();

        $response = $this->actingAs($livreur)
            ->post(route('livreur.deliveries.validate-by-code', $delivery), [
                'validation_code' => 'ABC123',
            ]);

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('error', 'La date actuelle n\'est pas comprise dans la période d\'abonnement.');
    }

    public function test_delivery_refused_when_quota_reached(): void
    {
        $livreur = $this->livreur();
        [$sub, $order, $delivery] = $this->createDeliveryWithSubscription([
            'total_days' => 5,
            'start_date' => now()->subDays(10)->format('Y-m-d'),
            'end_date' => now()->subDay()->format('Y-m-d'),
        ]);

        $delivery->livreur_id = $livreur->id;
        $delivery->save();

        $response = $this->actingAs($livreur)
            ->post(route('livreur.deliveries.validate-by-code', $delivery), [
                'validation_code' => 'ABC123',
            ]);

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('error');
    }

    public function test_delivery_refused_when_meal_already_delivered_today(): void
    {
        $livreur = $this->livreur();
        [$sub, $order, $delivery] = $this->createDeliveryWithSubscription();

        Order::factory()->delivered()->create([
            'subscription_id' => $sub->id,
            'agent_id' => $sub->agent_id,
            'client_id' => $sub->client_id,
            'delivery_date' => now()->format('Y-m-d'),
            'admin_validated_at' => now(),
        ]);

        $delivery->livreur_id = $livreur->id;
        $delivery->save();

        $response = $this->actingAs($livreur)
            ->post(route('livreur.deliveries.validate-by-code', $delivery), [
                'validation_code' => 'ABC123',
            ]);

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('error', 'Le nombre de repas autorisés aujourd\'hui pour cet abonnement est déjà atteint.');
    }

    public function test_delivery_refused_when_order_already_validated(): void
    {
        $livreur = $this->livreur();
        [$sub, $order, $delivery] = $this->createDeliveryWithSubscription([
            'total_days' => 30,
        ], [
            'client_validated_at' => now(),
        ]);

        $delivery->livreur_id = $livreur->id;
        $delivery->save();

        $response = $this->actingAs($livreur)
            ->post(route('livreur.deliveries.validate-by-code', $delivery), [
                'validation_code' => 'ABC123',
            ]);

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('error', 'Cette commande a déjà été validée par le client.');
    }

    public function test_delivery_refused_on_deliver_action_when_subscription_inactive(): void
    {
        $livreur = $this->livreur();
        [$sub, $order, $delivery] = $this->createDeliveryWithSubscription(['status' => 'expired']);

        $delivery->livreur_id = $livreur->id;
        $delivery->status = 'assigned';
        $delivery->save();

        $response = $this->actingAs($livreur)
            ->post(route('livreur.deliveries.deliver', $delivery));

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('error', 'L\'abonnement n\'est pas actif.');
    }

    public function test_non_subscription_order_allowed_when_not_validated(): void
    {
        $livreur = $this->livreur();
        $agent = User::factory()->agent()->create();
        $client = User::factory()->client()->create();

        $order = Order::factory()->confirmed()->create([
            'subscription_id' => null,
            'agent_id' => $agent->id,
            'client_id' => $client->id,
            'delivery_date' => now()->format('Y-m-d'),
            'client_validation_code' => 'XYZ789',
            'admin_validated_at' => now(),
        ]);

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
            'livreur_id' => $livreur->id,
            'status' => 'assigned',
        ]);

        $response = $this->actingAs($livreur)
            ->post(route('livreur.deliveries.validate-by-code', $delivery), [
                'validation_code' => 'XYZ789',
            ]);

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('reward');
    }

    public function test_non_subscription_order_refused_when_already_validated(): void
    {
        $livreur = $this->livreur();
        $agent = User::factory()->agent()->create();
        $client = User::factory()->client()->create();

        $order = Order::factory()->confirmed()->create([
            'subscription_id' => null,
            'agent_id' => $agent->id,
            'client_id' => $client->id,
            'delivery_date' => now()->format('Y-m-d'),
            'client_validation_code' => 'XYZ789',
            'admin_validated_at' => now(),
            'client_validated_at' => now(),
        ]);

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
            'livreur_id' => $livreur->id,
            'status' => 'assigned',
        ]);

        $response = $this->actingAs($livreur)
            ->post(route('livreur.deliveries.validate-by-code', $delivery), [
                'validation_code' => 'XYZ789',
            ]);

        $response->assertRedirect(route('livreur.deliveries.show', $delivery));
        $response->assertSessionHas('error', 'Cette commande a déjà été validée par le client.');
    }
}
