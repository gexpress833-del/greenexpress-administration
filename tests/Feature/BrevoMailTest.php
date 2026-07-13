<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BrevoMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_brevo_smtp_configuration_is_loaded(): void
    {
        $this->assertEquals('brevo', config('mail.mailers.brevo.transport'));
        $this->assertNotNull(config('mail.mailers.brevo.key'));
    }

    public function test_mail_from_address_is_configured(): void
    {
        $this->assertEquals('gexpress833@gmail.com', config('mail.from.address'));
        $this->assertEquals('Green Express', config('mail.from.name'));
    }

    public function test_password_reset_notification_is_sent(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        Notification::fake();

        $user->notify(new ResetPassword('test-token'));

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
