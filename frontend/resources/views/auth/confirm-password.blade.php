<x-guest-layout>
    <h1>Confirmar contraseña</h1>
    <p>Ingresá tu contraseña para continuar.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="form-stack">
        @csrf

        <div class="form-field">
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" type="password" name="password" required autofocus autocomplete="current-password" :aria-invalid="$errors->has('password') ? 'true' : 'false'" :aria-describedby="$errors->has('password') ? 'password-error' : null" />
            <x-input-error id="password-error" :messages="$errors->get('password')" />
        </div>

        <div class="form-actions">
            <x-primary-button>Confirmar</x-primary-button>
        </div>
    </form>

    <p><a href="{{ route('dashboard') }}">Volver al panel</a></p>
</x-guest-layout>
