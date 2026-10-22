<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_reset_link_is_sent_by_mail_to_the_user(): void
    {
        $user = User::factory()->create();

        $this->from('/forgot-password')->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect('/forgot-password')
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', __('passwords.sent'));

        $messages = Mail::mailer()->getSymfonyTransport()->messages();

        $this->assertCount(1, $messages);
        $message = $messages->first()->getOriginalMessage();
        $this->assertSame($user->email, $message->getTo()[0]->getAddress());
        $this->assertSame('Restablecé tu contraseña', $message->getSubject());
        $this->assertStringContainsString('/reset-password/', $message->getHtmlBody());
        $this->assertStringContainsString(urlencode($user->email), $message->getHtmlBody());
    }

    public function test_reset_link_uses_the_canonical_url_with_an_alternative_request_host(): void
    {
        config(['app.url' => 'https://practica.example.test']);
        $user = User::factory()->create();

        $this->post('https://untrusted.example.test/forgot-password', ['email' => $user->email])
            ->assertSessionHasNoErrors();

        $message = Mail::mailer()->getSymfonyTransport()->messages()->first()->getOriginalMessage();

        $this->assertStringContainsString('https://practica.example.test/reset-password/', $message->getHtmlBody());
        $this->assertStringNotContainsString('https://untrusted.example.test/reset-password/', $message->getHtmlBody());
    }

    public function test_reset_link_requests_are_throttled_without_sending_another_mail(): void
    {
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertSessionHasNoErrors();

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertSessionHasErrors(['email' => __('passwords.throttled')]);

        $this->assertCount(1, Mail::mailer()->getSymfonyTransport()->messages());
    }

    public function test_mail_transport_failure_returns_an_error_and_preserves_throttling(): void
    {
        Log::spy();
        $user = User::factory()->create();
        $transport = Mockery::mock(TransportInterface::class);
        $transport->shouldReceive('send')->once()
            ->andThrow(new TransportException('Sensitive SMTP details must not be logged.'));
        Mail::mailer()->setSymfonyTransport($transport);

        $this->from('/forgot-password')->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect('/forgot-password')
            ->assertSessionHasErrors(['email' => __('passwords.delivery_failed')])
            ->assertSessionMissing('status');

        Log::shouldHaveReceived('warning')->once()->with(
            'No se pudo enviar el correo de recuperación.',
            ['exception_class' => TransportException::class],
        );

        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertSessionHasErrors(['email' => __('passwords.throttled')])
            ->assertSessionMissing('status');
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $originalPasswordHash = $user->password;
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            $this->assertNotSame($originalPasswordHash, $user->fresh()->password);
            $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
            $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);

            $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'reused-password-123',
                'password_confirmation' => 'reused-password-123',
            ])->assertSessionHasErrors(['email' => __('passwords.token')]);

            $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));

            return true;
        });
    }
}
