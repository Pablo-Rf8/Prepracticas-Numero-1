<x-guest-layout>
    <h1>Recuperar contraseña</h1>
    <p>Ingresá tu correo para recibir un enlace de recuperación.</p>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="form-stack">
        @csrf

        <div class="form-field">
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" :aria-invalid="$errors->has('email') ? 'true' : 'false'" :aria-describedby="$errors->has('email') ? 'email-error' : null" />
            <x-input-error id="email-error" :messages="$errors->get('email')" />
        </div>

        <div class="form-actions">
            <x-primary-button>Enviar enlace de recuperación</x-primary-button>
        </div>
    </form>

    <p><a href="{{ route('login') }}">Iniciar sesión</a></p>
    <p><a href="{{ url('/') }}">Volver al inicio</a></p>
</x-guest-layout>
