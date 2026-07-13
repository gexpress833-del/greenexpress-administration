<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_password_reset_link(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        Notification::fake();

        $response = $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_reset_transport_failure_is_returned_as_a_user_error(): void
    {
        User::factory()->create(['email' => 'test@example.com']);
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andThrow(new \RuntimeException('Mail transport unavailable'));

        $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ])->assertSessionHasErrors('email');
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-password123', $user->fresh()->password));
    }

    public function test_password_reset_requires_valid_email(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_password_reset_requires_matching_passwords(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
