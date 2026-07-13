<?php

namespace Tests\Feature;

use App\Models\Meal;
use App\Models\Subscription;
use App\Models\SubscriptionType;
use App\Models\User;
use App\Services\SubscriptionDeliveryService;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealsPerDayTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_type_can_have_meals_per_day(): void
    {
        $type = SubscriptionType::factory()->create([
            'name' => 'Premium Mensuel',
            'duration_days' => 30,
            'meals_per_day' => 2,
            'price' => 140,
        ]);

        $this->assertEquals(2, $type->meals_per_day);
        $this->assertEquals(30, $type->duration_days);
    }

    public function test_generate_daily_orders_creates_multiple_orders_per_day(): void
    {
        $agent = User::factory()->agent()->create();
        $client = User::factory()->client()->create();

        $type = SubscriptionType::factory()->create([
            'duration_days' => 7,
            'meals_per_day' => 2,
        ]);

        $startDate = now()->isWeekend() ? now()->nextWeekday() : now();

        $subscription = Subscription::factory()->active()->create([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
            'subscription_type_id' => $type->id,
            'type' => 'weekly',
            'total_days' => 7,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $startDate->copy()->addDays(6)->format('Y-m-d'),
            'client_name' => $client->name,
            'client_phone' => $client->phone ?? '123456789',
        ]);

        $service = app(SubscriptionDeliveryService::class);
        $service->generateDailyOrders($subscription);

        $orders = $subscription->orders()->orderBy('delivery_date')->get();
        $this->assertGreaterThan(0, $orders->count());

        $firstDayOrders = $orders->filter(fn ($o) => $o->delivery_date->isSameDay($startDate));
        $this->assertEquals(2, $firstDayOrders->count(), 'Should have 2 orders for the first business day with meals_per_day=2');
        $this->assertEquals(10, $orders->count(), 'A weekly subscription should generate 5 business days of meals, multiplied by meals_per_day=2');
    }

    public function test_generate_daily_orders_creates_one_order_when_meals_per_day_is_one(): void
    {
        $agent = User::factory()->agent()->create();
        $client = User::factory()->client()->create();

        $type = SubscriptionType::factory()->create([
            'duration_days' => 7,
            'meals_per_day' => 1,
        ]);

        $startDate = now()->isWeekend() ? now()->nextWeekday() : now();

        $subscription = Subscription::factory()->active()->create([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
            'subscription_type_id' => $type->id,
            'type' => 'weekly',
            'total_days' => 7,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $startDate->copy()->addDays(6)->format('Y-m-d'),
            'client_name' => $client->name,
            'client_phone' => $client->phone ?? '123456789',
        ]);

        $service = app(SubscriptionDeliveryService::class);
        $service->generateDailyOrders($subscription);

        $firstDayOrders = $subscription->orders()->whereDate('delivery_date', $startDate)->get();
        $this->assertEquals(1, $firstDayOrders->count());
        $this->assertEquals(5, $subscription->orders()->count(), 'A weekly subscription should generate one meal for each of 5 business days');
    }

    public function test_monthly_subscription_generates_twenty_business_days_of_meals(): void
    {
        $agent = User::factory()->agent()->create();
        $client = User::factory()->client()->create();

        $type = SubscriptionType::factory()->create([
            'duration_days' => 30,
            'meals_per_day' => 1,
        ]);

        $startDate = now()->next(CarbonInterface::MONDAY);

        $subscription = Subscription::factory()->active()->create([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
            'subscription_type_id' => $type->id,
            'type' => 'monthly',
            'total_days' => 30,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $startDate->copy()->addDays(29)->format('Y-m-d'),
            'client_name' => $client->name,
            'client_phone' => $client->phone ?? '123456789',
        ]);

        app(SubscriptionDeliveryService::class)->generateDailyOrders($subscription);

        $orders = $subscription->orders()->orderBy('delivery_date')->get();

        $this->assertEquals(20, $orders->count());
        $this->assertTrue($orders->every(fn ($order) => $order->delivery_date->isWeekday()));
    }

    public function test_admin_can_create_subscription_type_with_meals_per_day(): void
    {
        $admin = User::factory()->admin()->create();
        $meal = Meal::factory()->create(['status' => 'available']);

        $response = $this->actingAs($admin)->post(route('admin.subscription-types.store'), [
            'name' => 'Premium Hebdo',
            'description' => '2 repas par jour',
            'price' => 35,
            'currency' => 'usd',
            'duration_days' => 7,
            'meals_per_day' => 2,
            'is_active' => true,
            'weekly_menu' => [],
        ]);

        $response->assertRedirect(route('admin.subscription-types.index'));

        $this->assertDatabaseHas('subscription_types', [
            'name' => 'Premium Hebdo',
            'meals_per_day' => 2,
            'duration_days' => 7,
        ]);
    }
}
