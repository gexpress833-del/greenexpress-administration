<?php

namespace Tests\Feature;

use App\Models\AgentPoint;
use App\Models\User;
use App\Services\PointWithdrawalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointWithdrawalTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_withdraw_points_to_any_mobile_money_operator(): void
    {
        $agent = User::factory()->agent()->create();
        AgentPoint::create([
            'agent_id' => $agent->id,
            'points' => 240,
            'value_usd' => 6,
            'description' => 'Test points',
            'earned_at' => now(),
        ]);

        $response = $this->actingAs($agent)->post(route('agent.withdrawals.store'), [
            'points' => 200,
            'mobile_money_operator' => 'Operateur libre',
            'mobile_money_number' => '0990000000',
        ]);

        $response->assertRedirect(route('agent.withdrawals.index'));
        $this->assertDatabaseHas('withdrawals', [
            'user_id' => $agent->id,
            'points' => 200,
            'amount_usd' => 5,
            'mobile_money_operator' => 'Operateur libre',
            'mobile_money_number' => '0990000000',
            'status' => 'pending',
        ]);
    }

    public function test_livreur_cannot_withdraw_before_five_dollars(): void
    {
        $livreur = User::factory()->livreur()->create();

        $response = $this->actingAs($livreur)->post(route('livreur.withdrawals.store'), [
            'points' => 199,
            'mobile_money_operator' => 'Operateur libre',
            'mobile_money_number' => '0990000000',
        ]);

        $response->assertSessionHasErrors('points');
        $this->assertDatabaseCount('withdrawals', 0);
    }

    public function test_client_has_no_available_points(): void
    {
        $client = User::factory()->client()->create();

        $this->assertSame(0, app(PointWithdrawalService::class)->availablePoints($client));
    }
}
