<x-app-layout>
    <x-slot name="header">
        <h1>Usuarios</h1>
    </x-slot>

    @if ($users->isNotEmpty())
        <p>Mostrando {{ number_format($users->firstItem(), 0, ',', '.') }} al {{ number_format($users->lastItem(), 0, ',', '.') }} de {{ number_format($users->total(), 0, ',', '.') }} usuarios.</p>

        <div class="table-scroll" role="region" aria-label="Lista de usuarios" tabindex="0">
            <table class="users-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Fecha de registro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->created_at?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p>{{ $users->total() === 0 ? 'Todavía no hay usuarios registrados.' : 'No hay usuarios en esta página.' }}</p>
    @endif

    @if ($users->total() > 0)
        <nav class="pagination" aria-label="Paginación de usuarios">
            @if ($users->onFirstPage())
                <span aria-disabled="true">Anterior</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" rel="prev">Anterior</a>
            @endif

            <span>Página {{ number_format($users->currentPage(), 0, ',', '.') }} de {{ number_format($users->lastPage(), 0, ',', '.') }}</span>

            @if ($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" rel="next">Siguiente</a>
            @else
                <span aria-disabled="true">Siguiente</span>
            @endif
        </nav>
    @endif

    <p><a href="{{ route('dashboard') }}">Volver al dashboard</a></p>
</x-app-layout>
