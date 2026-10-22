<section class="profile-section" aria-labelledby="update-password-heading">
    <h2 id="update-password-heading">Cambiar contraseña</h2>

    <form method="post" action="{{ route('password.update') }}" class="form-stack">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Contraseña actual')" />
            <x-password-input id="update_password_current_password" name="current_password" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Nueva contraseña')" />
            <x-password-input id="update_password_password" name="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirmar nueva contraseña')" />
            <x-password-input id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" />
        </div>

        <div class="form-actions">
            <x-primary-button>Actualizar contraseña</x-primary-button>

            @if (session('status') === 'password-updated')
                <p class="notice" role="status">Guardado.</p>
            @endif
        </div>
    </form>
</section>
