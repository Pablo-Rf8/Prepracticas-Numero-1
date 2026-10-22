<section class="profile-section" aria-labelledby="profile-information-heading">
    <h2 id="profile-information-heading">Datos personales</h2>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="form-stack">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nombre completo')" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p>Tu correo electrónico está pendiente de verificación.</p>
                    <button form="send-verification">Reenviar correo de verificación</button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="notice" role="status">Enviamos un nuevo enlace de verificación a tu correo electrónico.</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="form-actions">
            <x-primary-button>Guardar cambios</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p class="notice" role="status">Guardado.</p>
            @endif
        </div>
    </form>
</section>
