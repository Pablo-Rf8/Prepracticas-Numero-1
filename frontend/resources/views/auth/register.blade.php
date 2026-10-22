<x-guest-layout>
    <h1>Crear cuenta</h1>

    <form method="POST" action="{{ route('register') }}" class="form-stack">
        @csrf

        <div class="form-field">
            <x-input-label for="name" value="Nombre completo" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" :aria-invalid="$errors->has('name') ? 'true' : 'false'" :aria-describedby="$errors->has('name') ? 'name-error' : null" />
            <x-input-error id="name-error" :messages="$errors->get('name')" />
        </div>

        <div class="form-field">
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" :aria-invalid="$errors->has('email') ? 'true' : 'false'" :aria-describedby="$errors->has('email') ? 'email-error' : null" />
            <x-input-error id="email-error" :messages="$errors->get('email')" />
        </div>

        <div class="form-field">
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" :aria-describedby="$errors->has('password') ? 'password-hint password-error' : 'password-hint'" :aria-invalid="$errors->has('password') ? 'true' : 'false'" />
            <p id="password-hint">Mínimo 8 caracteres.</p>
            <x-input-error id="password-error" :messages="$errors->get('password')" />
        </div>

        <div class="form-field">
            <x-input-label for="password_confirmation" value="Confirmar contraseña" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" :aria-invalid="$errors->has('password_confirmation') ? 'true' : 'false'" :aria-describedby="$errors->has('password_confirmation') ? 'password-confirmation-error' : null" />
            <x-input-error id="password-confirmation-error" :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="form-actions">
            <x-primary-button>Crear cuenta</x-primary-button>
        </div>
    </form>

    <p><a href="{{ route('login') }}">Iniciar sesión</a></p>
    <p><a href="{{ url('/') }}">Volver al inicio</a></p>
</x-guest-layout>
