<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_login_returns_a_spanish_error(): void
    {
        $this->from('/login')->post('/login', [
            'email' => 'incorrecto@example.test',
            'password' => 'incorrecta',
        ])->assertSessionHasErrors([
            'email' => 'El correo o la contraseña no son correctos.',
        ]);
    }

    public function test_registration_validation_messages_are_in_spanish(): void
    {
        $this->from('/register')->post('/register', [
            'name' => '',
            'email' => 'correo-invalido',
            'password' => 'corta',
            'password_confirmation' => 'otra',
        ])->assertSessionHasErrors([
            'name' => 'El campo nombre es obligatorio.',
            'email' => 'Ingresá un correo electrónico válido.',
            'password' => 'La confirmación de contraseña no coincide.',
        ]);
    }

    public function test_password_recovery_notification_is_in_spanish(): void
    {
        $notification = new ResetPassword('test-token');
        $message = $notification->toMail(User::factory()->make());

        $this->assertSame('Restablecé tu contraseña', $message->subject);
        $this->assertSame('Restablecer contraseña', $message->actionText);
    }
}
