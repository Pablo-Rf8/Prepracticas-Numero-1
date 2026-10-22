<section class="profile-section" aria-labelledby="delete-account-heading">
    <h2 id="delete-account-heading">Eliminar cuenta</h2>
    <p>La cuenta y sus datos se eliminarán permanentemente.</p>

    <div>
        <x-danger-button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >Eliminar mi cuenta</x-danger-button>
    </div>

    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        labelled-by="confirm-user-deletion-heading"
        described-by="confirm-user-deletion-description"
        max-width="lg"
        focusable
    >
        <form method="post" action="{{ route('profile.destroy') }}" class="modal-form form-stack">
            @csrf
            @method('delete')

            <div>
                <h2 id="confirm-user-deletion-heading">¿Eliminar tu cuenta?</h2>
                <p id="confirm-user-deletion-description">Escribí tu contraseña para confirmar. Esta acción no se puede deshacer.</p>
            </div>

            <div>
                <x-input-label for="password" :value="__('Contraseña actual')" />
                <x-password-input id="password" name="password" autocomplete="current-password" required />
                <x-input-error :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="form-actions">
                <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                <x-danger-button>Eliminar cuenta</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
