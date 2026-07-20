<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\OrderCreated;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_endpoint_merges_database_and_app_notifications(): void
    {
        $admin = User::factory()->admin()->create();
        $agent = User::factory()->agent()->create();
        $order = Order::factory()->create(['agent_id' => $agent->id]);

        $admin->notify(new OrderCreated($order));
        app(NotificationService::class)->notify(
            $admin,
            'information',
            'Notification application',
            'Message application',
        );

        $this->actingAs($admin)
            ->getJson(route('notifications.index'))
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment(['source' => 'laravel'])
            ->assertJsonFragment(['source' => 'app']);
    }

    public function test_agent_receives_subscription_created_notification(): void
    {
        $agent = User::factory()->agent()->create();
        $client = User::factory()->client()->create();

        $subscription = Subscription::factory()->create([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
            'status' => 'pending',
        ]);

        app(NotificationService::class)->agentSubscriptionCreated($agent, $subscription);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $agent->id,
            'category' => 'subscription',
            'type' => 'subscription_created',
        ]);

        $notif = Notification::where('user_id', $agent->id)->first();
        $this->assertStringContainsString('Nouvelle demande enregistrée', $notif->title);
    }

    public function test_agent_receives_reward_notification_on_subscription_validation(): void
    {
        $agent = User::factory()->agent()->create();
        $client = User::factory()->client()->create();

        $subscription = Subscription::factory()->create([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
            'total_days' => 30,
        ]);

        app(NotificationService::class)->agentSubscriptionValidated($agent, $subscription, 50);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $agent->id,
            'category' => 'reward',
            'type' => 'subscription_validated',
        ]);

        $notif = Notification::where('user_id', $agent->id)->first();
        $this->assertStringContainsString('50 points', $notif->message);
    }

    public function test_agent_receives_renewal_notification(): void
    {
        $agent = User::factory()->agent()->create();
        $client = User::factory()->client()->create();

        $subscription = Subscription::factory()->create([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
        ]);

        app(NotificationService::class)->agentSubscriptionRenewed($agent, $subscription);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $agent->id,
            'category' => 'reward',
            'type' => 'subscription_renewed',
        ]);
    }

    public function test_livreur_receives_delivery_assigned_notification(): void
    {
        $livreur = User::factory()->livreur()->create();
        $agent = User::factory()->agent()->create();
        $client = User::factory()->client()->create();

        $order = Order::factory()->create([
            'agent_id' => $agent->id,
            'client_id' => $client->id,
            'client_name' => 'Test Client',
            'delivery_address' => '123 Test St',
        ]);

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
            'livreur_id' => $livreur->id,
        ]);

        app(NotificationService::class)->livreurDeliveryAssigned($livreur, $delivery);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $livreur->id,
            'category' => 'delivery',
            'type' => 'delivery_assigned',
        ]);
    }

    public function test_livreur_receives_subscription_delivery_notification(): void
    {
        $livreur = User::factory()->livreur()->create();
        $agent = User::factory()->agent()->create();
        $client = User::factory()->client()->create();

        $order = Order::factory()->create([
            'agent_id' => $agent->id,
            'client_id' => $client->id,
            'client_name' => 'Sub Client',
        ]);

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
            'livreur_id' => $livreur->id,
        ]);

        app(NotificationService::class)->livreurSubscriptionDeliveryAssigned($livreur, $delivery);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $livreur->id,
            'category' => 'delivery',
            'type' => 'delivery_assigned',
        ]);

        $notif = Notification::where('user_id', $livreur->id)->first();
        $this->assertStringContainsString('Livraison d\'abonnement', $notif->title);
    }

    public function test_livreur_receives_delivery_validated_notification(): void
    {
        $livreur = User::factory()->livreur()->create();
        $agent = User::factory()->agent()->create();

        $order = Order::factory()->create([
            'agent_id' => $agent->id,
            'client_name' => 'Validated Client',
        ]);

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
            'livreur_id' => $livreur->id,
        ]);

        app(NotificationService::class)->livreurDeliveryValidated($livreur, $delivery, false);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $livreur->id,
            'category' => 'reward',
            'type' => 'delivery_validated',
        ]);

        $notif = Notification::where('user_id', $livreur->id)->first();
        $this->assertStringContainsString('15 points', $notif->message);
    }

    public function test_livreur_receives_delivery_pending_notification(): void
    {
        $livreur = User::factory()->livreur()->create();
        $agent = User::factory()->agent()->create();

        $order = Order::factory()->create([
            'agent_id' => $agent->id,
        ]);

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
            'livreur_id' => $livreur->id,
        ]);

        app(NotificationService::class)->livreurDeliveryPending($livreur, $delivery);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $livreur->id,
            'category' => 'information',
            'type' => 'delivery_pending',
        ]);
    }

    public function test_notification_model_category_label(): void
    {
        $user = User::factory()->agent()->create();

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Test',
            'message' => 'Test message',
            'type' => 'test',
            'category' => 'reward',
            'is_read' => false,
        ]);

        $notif = Notification::where('user_id', $user->id)->first();
        $this->assertEquals('Récompense', $notif->category_label);
        $this->assertEquals('🏆', $notif->icon);
    }

    public function test_notification_history_filters_by_category(): void
    {
        $user = User::factory()->agent()->create();

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Reward notif',
            'message' => 'You got points',
            'type' => 'test',
            'category' => 'reward',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Delivery notif',
            'message' => 'New delivery',
            'type' => 'test',
            'category' => 'delivery',
            'is_read' => false,
        ]);

        $response = $this->actingAs($user)
            ->get(route('notifications.history', ['category' => 'reward']));

        $response->assertStatus(200);
        $response->assertSee('Reward notif');
        $response->assertDontSee('Delivery notif');
    }

    public function test_client_receives_subscription_validated_notification(): void
    {
        $client = User::factory()->client()->create();
        $agent = User::factory()->agent()->create();

        $subscription = Subscription::factory()->create([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
            'status' => 'active',
            'end_date' => now()->addDays(30),
        ]);

        app(NotificationService::class)->clientSubscriptionValidated($client, $subscription);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $client->id,
            'category' => 'subscription',
            'type' => 'subscription_activated',
        ]);

        $notif = Notification::where('user_id', $client->id)->first();
        $this->assertStringContainsString('Abonnement activé', $notif->title);
    }

    public function test_client_receives_delivery_assigned_notification(): void
    {
        $client = User::factory()->client()->create();
        $agent = User::factory()->agent()->create();
        $livreur = User::factory()->livreur()->create();

        $order = Order::factory()->create([
            'agent_id' => $agent->id,
            'client_id' => $client->id,
            'client_name' => 'Test Client',
            'code' => 'CMD-001',
        ]);

        $delivery = Delivery::factory()->create([
            'order_id' => $order->id,
            'livreur_id' => $livreur->id,
        ]);

        app(NotificationService::class)->clientDeliveryAssigned($client, $delivery);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $client->id,
            'category' => 'delivery',
            'type' => 'delivery_assigned',
        ]);

        $notif = Notification::where('user_id', $client->id)->first();
        $this->assertStringContainsString('Livreur en route', $notif->title);
    }

    public function test_client_receives_order_delivered_notification(): void
    {
        $client = User::factory()->client()->create();
        $agent = User::factory()->agent()->create();

        $order = Order::factory()->create([
            'agent_id' => $agent->id,
            'client_id' => $client->id,
            'code' => 'CMD-002',
        ]);

        app(NotificationService::class)->clientOrderDelivered($client, $order);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $client->id,
            'category' => 'success',
            'type' => 'order_delivered',
        ]);

        $notif = Notification::where('user_id', $client->id)->first();
        $this->assertStringContainsString('Commande livrée', $notif->title);
    }

    public function test_client_receives_subscription_expiring_notification(): void
    {
        $client = User::factory()->client()->create();
        $agent = User::factory()->agent()->create();

        $subscription = Subscription::factory()->create([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
            'status' => 'active',
            'end_date' => now()->addDays(2),
        ]);

        app(NotificationService::class)->clientSubscriptionExpiring($client, $subscription, 2);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $client->id,
            'category' => 'alert',
            'type' => 'subscription_expiring',
        ]);

        $notif = Notification::where('user_id', $client->id)->first();
        $this->assertStringContainsString('expire bientôt', $notif->title);
    }

    public function test_client_receives_renewal_sent_notification(): void
    {
        $client = User::factory()->client()->create();
        $agent = User::factory()->agent()->create();

        $subscription = Subscription::factory()->create([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
            'type' => 'weekly',
        ]);

        app(NotificationService::class)->clientSubscriptionRenewalSent($client, $subscription);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $client->id,
            'category' => 'subscription',
            'type' => 'subscription_renewal_sent',
        ]);
    }

    public function test_client_receives_suspension_notification(): void
    {
        $client = User::factory()->client()->create();
        $agent = User::factory()->agent()->create();

        $subscription = Subscription::factory()->create([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
        ]);

        app(NotificationService::class)->clientSubscriptionSuspended($client, $subscription, 'Voyage', 5);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $client->id,
            'category' => 'subscription',
            'type' => 'subscription_suspended',
        ]);

        $notif = Notification::where('user_id', $client->id)->first();
        $this->assertStringContainsString('5 jour(s)', $notif->message);
    }

    public function test_cuisinier_receives_new_order_notification(): void
    {
        $cuisinier = User::factory()->cuisinier()->create();
        $agent = User::factory()->agent()->create();

        $order = Order::factory()->create([
            'agent_id' => $agent->id,
            'client_name' => 'Test Client',
            'code' => 'CMD-CHEF-001',
        ]);

        app(NotificationService::class)->cuisinierNewOrder($cuisinier, $order);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $cuisinier->id,
            'category' => 'order',
            'type' => 'cuisinier_new_order',
        ]);

        $notif = Notification::where('user_id', $cuisinier->id)->first();
        $this->assertStringContainsString('Nouvelle commande à préparer', $notif->title);
    }

    public function test_admin_receives_preparation_started_notification(): void
    {
        $admin = User::factory()->admin()->create();
        $agent = User::factory()->agent()->create();

        $order = Order::factory()->create([
            'agent_id' => $agent->id,
            'code' => 'CMD-CHEF-002',
        ]);

        app(NotificationService::class)->cuisinierPreparationStarted($admin, $order);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $admin->id,
            'category' => 'order',
            'type' => 'cuisinier_preparation_started',
        ]);

        $notif = Notification::where('user_id', $admin->id)->first();
        $this->assertStringContainsString('Préparation en cours', $notif->title);
    }

    public function test_livreur_receives_order_ready_notification(): void
    {
        $livreur = User::factory()->livreur()->create();
        $agent = User::factory()->agent()->create();

        $order = Order::factory()->create([
            'agent_id' => $agent->id,
            'client_name' => 'Ready Client',
            'delivery_address' => '123 Ready St',
            'code' => 'CMD-CHEF-003',
        ]);

        app(NotificationService::class)->cuisinierOrderReady($livreur, $order);

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $livreur->id,
            'category' => 'order',
            'type' => 'cuisinier_order_ready',
        ]);

        $notif = Notification::where('user_id', $livreur->id)->first();
        $this->assertStringContainsString('prête pour livraison', $notif->title);
    }
}
