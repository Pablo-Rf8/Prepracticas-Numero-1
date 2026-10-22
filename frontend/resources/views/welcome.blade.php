<x-guest-layout>
    <h1>Preprácticas</h1>
    @auth
        <p><a href="{{ route('dashboard') }}">Ir al dashboard</a></p>
    @else
        <p><a href="{{ route('login') }}">Iniciar sesión</a></p>
        <p><a href="{{ route('register') }}">Crear cuenta</a></p>
    @endauth
</x-guest-layout>
