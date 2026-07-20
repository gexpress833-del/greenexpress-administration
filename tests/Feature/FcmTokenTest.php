<?php

namespace Tests\Feature;

use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FcmTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_register_a_fcm_token(): void
    {
        $user = User::factory()->agent()->create();
        $token = str_repeat('fcm-token-', 8);

        $response = $this->actingAs($user)->postJson(route('notifications.fcm-token.store'), [
            'token' => $token,
            'platform' => 'android',
            'device_id' => 'device-1',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('fcm_tokens', [
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $token),
            'platform' => 'android',
            'device_id' => 'device-1',
            'revoked_at' => null,
        ]);
    }

    public function test_registering_the_same_token_updates_its_owner_and_device(): void
    {
        $firstUser = User::factory()->agent()->create();
        $secondUser = User::factory()->client()->create();
        $token = str_repeat('same-token-', 8);

        $this->actingAs($firstUser)->postJson(route('notifications.fcm-token.store'), ['token' => $token]);
        $this->actingAs($secondUser)->postJson(route('notifications.fcm-token.store'), [
            'token' => $token,
            'platform' => 'ios',
            'device_id' => 'device-2',
        ])->assertOk();

        $this->assertDatabaseCount('fcm_tokens', 1);
        $this->assertDatabaseHas('fcm_tokens', [
            'user_id' => $secondUser->id,
            'platform' => 'ios',
            'device_id' => 'device-2',
        ]);
    }

    public function test_user_can_revoke_only_their_token(): void
    {
        $user = User::factory()->agent()->create();
        $token = str_repeat('revoke-token-', 8);
        FcmToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'token_hash' => hash('sha256', $token),
            'platform' => 'web',
            'last_used_at' => now(),
        ]);

        $this->actingAs($user)->deleteJson(route('notifications.fcm-token.destroy'), ['token' => $token])
            ->assertOk();

        $this->assertDatabaseMissing('fcm_tokens', ['revoked_at' => null]);
    }
}
