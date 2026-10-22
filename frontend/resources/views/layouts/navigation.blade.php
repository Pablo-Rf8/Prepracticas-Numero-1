<nav class="simple-navigation" aria-label="Navegación principal">
    <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>Dashboard</a>
    <a href="{{ route('users.index') }}" @if(request()->routeIs('users.*')) aria-current="page" @endif>Usuarios</a>
    <a href="{{ route('profile.edit') }}" @if(request()->routeIs('profile.*')) aria-current="page" @endif>Mi perfil</a>
    <button type="button" x-data="" x-on:click="$dispatch('open-modal', 'confirm-logout')">Cerrar sesión</button>
</nav>

<x-modal
    name="confirm-logout"
    labelled-by="confirm-logout-heading"
    described-by="confirm-logout-description"
    max-width="lg"
    focusable
>
    <form method="POST" action="{{ route('logout') }}" class="form-stack">
        @csrf

        <div>
            <h2 id="confirm-logout-heading">¿Querés cerrar sesión?</h2>
            <p id="confirm-logout-description">Podés cancelar para seguir usando tu cuenta.</p>
        </div>

        <div class="form-actions">
            <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
            <x-primary-button>Cerrar sesión</x-primary-button>
        </div>
    </form>
</x-modal>
