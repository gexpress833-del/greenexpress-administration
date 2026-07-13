<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use App\Services\StatisticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_displays_subscription_statistics(): void
    {
        $admin = User::factory()->admin()->create();

        Subscription::factory()->active()->count(3)->create();
        Subscription::factory()->create(['status' => 'expired']);
        Subscription::factory()->count(2)->create(['status' => 'pending']);

        Order::factory()->delivered()->count(5)->create([
            'client_validated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Abonnements actifs');
        $response->assertSee('Abonnements expir');
        $response->assertSee('Renouvellements du mois');
        $response->assertSee('Revenus hebdomadaires');
        $response->assertSee('Revenus mensuels');
        $response->assertSee('Repas livr');
        $response->assertSee('Repas non r');
        $response->assertSee('Taux de renouvellement');
        $response->assertSee('Nouveaux abonn');
    }

    public function test_dashboard_kpi_contains_all_subscription_keys(): void
    {
        $service = app(StatisticsService::class);
        $kpi = $service->getDashboardKpi();

        $this->assertArrayHasKey('subscriptions', $kpi);
        $this->assertArrayHasKey('active', $kpi['subscriptions']);
        $this->assertArrayHasKey('expired', $kpi['subscriptions']);
        $this->assertArrayHasKey('renewals_this_month', $kpi['subscriptions']);
        $this->assertArrayHasKey('weekly_revenue', $kpi['subscriptions']);
        $this->assertArrayHasKey('monthly_revenue', $kpi['subscriptions']);
        $this->assertArrayHasKey('meals_delivered', $kpi['subscriptions']);
        $this->assertArrayHasKey('meals_not_picked_up', $kpi['subscriptions']);
        $this->assertArrayHasKey('renewal_rate', $kpi['subscriptions']);
        $this->assertArrayHasKey('new_subscribers', $kpi['subscriptions']);
    }

    public function test_dashboard_counts_active_subscriptions_correctly(): void
    {
        Subscription::factory()->active()->count(4)->create();
        Subscription::factory()->create(['status' => 'expired']);

        $service = app(StatisticsService::class);
        $kpi = $service->getDashboardKpi();

        $this->assertEquals(4, $kpi['subscriptions']['active']);
        $this->assertEquals(1, $kpi['subscriptions']['expired']);
    }

    public function test_dashboard_counts_monthly_renewals_without_treating_column_as_string(): void
    {
        $client = User::factory()->client()->create();

        Subscription::factory()->create([
            'client_id' => $client->id,
            'created_at' => now()->subMonth(),
        ]);

        Subscription::factory()->create([
            'client_id' => $client->id,
            'created_at' => now(),
        ]);

        $kpi = app(StatisticsService::class)->getDashboardKpi();

        $this->assertSame(1, $kpi['subscriptions']['renewals_this_month']);
    }

    public function test_admin_expiring_subscriptions_route_is_not_captured_as_subscription_id(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.subscriptions.expiring'))
            ->assertStatus(200);
    }

    public function test_admin_dashboard_rejects_invalid_date_filters_without_server_error(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['start' => 'invalid-date']))
            ->assertSessionHasErrors('start');
    }

    public function test_admin_dashboard_rejects_an_end_date_before_the_start_date(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard', [
                'start' => '2026-07-10',
                'end' => '2026-07-09',
            ]))
            ->assertSessionHasErrors('end');
    }

    public function test_admin_dashboard_does_not_render_mojibake_text(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Commandes validées');
        $response->assertDontSee('validÃ');
        $response->assertDontSee('â€”');
    }
}
