<x-guest-layout>
    <h1>Restablecer contraseña</h1>

    @if ($hasValidLink)
        <form method="POST" action="{{ route('password.store') }}" class="form-stack">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <x-input-error :messages="$errors->get('email')" />
            <x-input-error :messages="$errors->get('token')" />

            <div class="form-field">
                <x-input-label for="password" value="Nueva contraseña" />
                <x-password-input id="password" name="password" required autofocus autocomplete="new-password" :aria-describedby="$errors->has('password') ? 'password-hint password-error' : 'password-hint'" :aria-invalid="$errors->has('password') ? 'true' : 'false'" />
                <p id="password-hint">Mínimo 8 caracteres.</p>
                <x-input-error id="password-error" :messages="$errors->get('password')" />
            </div>

            <div class="form-field">
                <x-input-label for="password_confirmation" value="Confirmar nueva contraseña" />
                <x-password-input id="password_confirmation" name="password_confirmation" required autocomplete="new-password" :aria-invalid="$errors->has('password_confirmation') ? 'true' : 'false'" :aria-describedby="$errors->has('password_confirmation') ? 'password-confirmation-error' : null" />
                <x-input-error id="password-confirmation-error" :messages="$errors->get('password_confirmation')" />
            </div>

            <div class="form-actions">
                <x-primary-button>Guardar contraseña</x-primary-button>
            </div>
        </form>
    @else
        <p role="alert">{{ __('passwords.token') }}</p>
        <p><a href="{{ route('password.request') }}">Solicitar otro enlace</a></p>
    @endif

    <p><a href="{{ route('login') }}">Iniciar sesión</a></p>
</x-guest-layout>
