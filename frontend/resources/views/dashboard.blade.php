<x-app-layout>
    <x-slot name="header">
        <h1>Dashboard</h1>
    </x-slot>

    <p>Hola, {{ Auth::user()->name }}. Has iniciado sesión.</p>
    <p>Correo: {{ Auth::user()->email }}</p>
    <p><a class="btn-primary" href="{{ route('users.index') }}">Ver usuarios</a></p>
    <p><a href="{{ route('profile.edit') }}">Editar mi perfil</a></p>
</x-app-layout>
