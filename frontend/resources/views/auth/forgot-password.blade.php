<x-guest-layout>
    @if (session('status') === __('passwords.sent'))
        <h1>Revisá tu correo</h1>

        <x-auth-session-status class="notice-success" :status="session('status')" />

        <p>Abrí el enlace que recibiste para crear una nueva contraseña. Si no lo encontrás, revisá la carpeta Spam.</p>
        <p><a href="{{ route('password.request') }}">Enviar otro enlace</a></p>
    @else
        <h1>Recuperar contraseña</h1>
        <p>Ingresá tu correo para recibir un enlace de recuperación.</p>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="form-stack" x-data="{ sending: false }" x-on:submit="if (sending) { $event.preventDefault(); } else { sending = true; }" x-bind:aria-busy="sending">
            @csrf

            <div class="form-field">
                <x-input-label for="email" value="Correo electrónico" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" :aria-invalid="$errors->has('email') ? 'true' : 'false'" :aria-describedby="$errors->has('email') ? 'email-error' : null" />
                <x-input-error id="email-error" :messages="$errors->get('email')" />
            </div>

            <div class="form-actions">
                <x-primary-button x-bind:disabled="sending" x-text="sending ? 'Enviando…' : 'Enviar enlace de recuperación'">Enviar enlace de recuperación</x-primary-button>
            </div>
        </form>
    @endif

    <p><a href="{{ route('login') }}">Iniciar sesión</a></p>
    <p><a href="{{ url('/') }}">Volver al inicio</a></p>
</x-guest-layout>
