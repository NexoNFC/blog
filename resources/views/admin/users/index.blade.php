@extends('layouts.admin')

@section('title', 'Usuarios')
@section('heading', 'Usuarios')
@section('subtitle', 'Cuentas de administradores')

@section('actions')
    @can('users.create')
        <x-ui.button href="{{ route('admin.users.create') }}">Nuevo usuario</x-ui.button>
    @endcan
@endsection

@section('content')
    <x-ui.table>
        <thead>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Correo</th>
                <th scope="col">Rol</th>
                <th scope="col">Estado</th>
                <th scope="col"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                @php
                    $role = $user->roles->first()?->name;
                    $roleLabel = match ($role) {
                        'admin' => 'Administrador',
                        default => '—',
                    };
                    $isLastActiveAdministrator = $user->isAdministrator() && $user->is_active && $activeAdministratorCount <= 1;
                @endphp
                <tr>
                    <th scope="row" class="whitespace-nowrap">{{ $user->name }}</th>
                    <td class="text-secondary-light">{{ $user->email }}</td>
                    <td>{{ $roleLabel }}</td>
                    <td>
                        <x-ui.badge :tone="$user->is_active ? 'success' : 'warning'">
                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                        </x-ui.badge>
                    </td>
                    <td>
                        <div class="flex flex-wrap items-center justify-end gap-2">
                            @can('update', $user)
                                <x-ui.button href="{{ route('admin.users.edit', $user) }}" variant="secondary" size="sm">
                                    Editar
                                </x-ui.button>
                            @endcan

                            @can('delete', $user)
                                @if ($isLastActiveAdministrator)
                                    <span class="text-xs text-secondary-light">Último administrador</span>
                                @else
                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.destroy', $user) }}"
                                        onsubmit="return confirm('¿Eliminar este usuario? Esta acción no se puede deshacer.')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="danger" size="sm">
                                            Eliminar
                                        </x-ui.button>
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-ui.empty-state embedded title="Sin usuarios" description="No hay usuarios registrados." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>
@endsection
