<x-guest-layout>
    <h1>Verificar correo</h1>
    <p>Abrí el enlace enviado a tu correo para verificar tu cuenta.</p>

    @if (session('status') === 'verification-link-sent')
        <div class="notice" role="status">Enviamos un nuevo enlace de verificación a tu correo electrónico.</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="form-stack">
        @csrf

        <div class="form-actions">
            <x-primary-button>Reenviar enlace de verificación</x-primary-button>
        </div>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button type="submit">Cerrar sesión</button>
    </form>

    <p><a href="{{ url('/') }}">Volver al inicio</a></p>
</x-guest-layout>
