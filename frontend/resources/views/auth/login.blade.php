<x-guest-layout>
    <h1>Iniciar sesión</h1>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="form-stack">
        @csrf

        <div class="form-field">
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" :aria-invalid="$errors->has('email') ? 'true' : 'false'" :aria-describedby="$errors->has('email') ? 'email-error' : null" />
            <x-input-error id="email-error" :messages="$errors->get('email')" />
        </div>

        <div class="form-field">
            <x-input-label for="password" value="Contraseña" />
            <x-password-input id="password" name="password" required autocomplete="current-password" :aria-invalid="$errors->has('password') ? 'true' : 'false'" :aria-describedby="$errors->has('password') ? 'password-error' : null" />
            <x-input-error id="password-error" :messages="$errors->get('password')" />
        </div>

        <div>
            <label for="remember_me">
                <input id="remember_me" type="checkbox" name="remember" @checked(old('remember'))>
                <span>Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <p><a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a></p>
            @endif
        </div>

        <div class="form-actions">
            <x-primary-button>Iniciar sesión</x-primary-button>
        </div>
    </form>

    <p><a href="{{ route('register') }}">Crear cuenta</a></p>
    <p><a href="{{ url('/') }}">Volver al inicio</a></p>
</x-guest-layout>
