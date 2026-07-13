<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_admin_dashboard_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password_changed_at' => now()]);
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_orders_loads(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password_changed_at' => now()]);
        $response = $this->actingAs($admin)->get('/admin/orders');
        $response->assertStatus(200);
    }

    public function test_agent_dashboard_loads(): void
    {
        $agent = User::factory()->create(['role' => 'agent', 'password_changed_at' => now()]);
        $response = $this->actingAs($agent)->get('/agent/dashboard');
        $response->assertStatus(200);
    }

    public function test_livreur_dashboard_loads(): void
    {
        $livreur = User::factory()->create(['role' => 'livreur', 'password_changed_at' => now()]);
        $response = $this->actingAs($livreur)->get('/livreur/dashboard');
        $response->assertStatus(200);
    }

    public function test_scheduler_endpoint_rejects_get_requests_and_missing_secrets(): void
    {
        config(['services.schedule.secret' => 'test-schedule-secret']);

        $this->get(route('internal.schedule-run'))->assertMethodNotAllowed();
        $this->postJson(route('internal.schedule-run'))->assertForbidden();
        $this->withToken('wrong-secret')->postJson(route('internal.schedule-run'))->assertForbidden();
    }

    public function test_scheduler_endpoint_accepts_a_valid_bearer_secret(): void
    {
        config(['services.schedule.secret' => 'test-schedule-secret']);
        Artisan::shouldReceive('call')->once()->with('schedule:run')->andReturn(0);
        Artisan::shouldReceive('output')->once()->andReturn('');

        $this->withToken('test-schedule-secret')
            ->postJson(route('internal.schedule-run'))
            ->assertOk()
            ->assertJson(['status' => 'ok']);
    }

    public function test_client_dashboard_loads(): void
    {
        $client = User::factory()->create(['role' => 'client', 'password_changed_at' => now()]);
        try {
            $response = $this->actingAs($client)->get('/client/dashboard');
            $response->assertStatus(200);
        } catch (\Throwable $e) {
            $this->fail($e->getMessage()."\n".$e->getTraceAsString());
        }
    }
}
