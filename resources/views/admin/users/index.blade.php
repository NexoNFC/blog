@extends('layouts.admin')

@section('title', 'Usuarios')
@section('heading', 'Usuarios')
@section('subtitle', 'Cuentas de administradores y editores')

@section('actions')
    @can('users.create')
        <x-ui.button href="{{ route('admin.users.create') }}">Nuevo usuario</x-ui.button>
    @endcan
@endsection

@section('content')
    <x-ui.table>
        <thead class="border-b border-white/40 bg-white/30 text-xs uppercase tracking-wide text-secondary-light">
            <tr>
                <th class="px-4 py-3 font-semibold">Nombre</th>
                <th class="px-4 py-3 font-semibold">Correo</th>
                <th class="px-4 py-3 font-semibold">Rol</th>
                <th class="px-4 py-3 font-semibold">Estado</th>
                <th class="px-4 py-3 font-semibold"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/40">
            @forelse ($users as $user)
                @php
                    $role = $user->roles->first()?->name;
                    $roleLabel = match ($role) {
                        'admin' => 'Administrador',
                        'editor' => 'Editor',
                        default => '—',
                    };
                    $isLastActiveAdministrator = $user->isAdministrator() && $user->is_active && $activeAdministratorCount <= 1;
                @endphp
                <tr class="transition hover:bg-white/35">
                    <td class="px-4 py-3 font-medium text-secondary">{{ $user->name }}</td>
                    <td class="px-4 py-3 text-secondary-light">{{ $user->email }}</td>
                    <td class="px-4 py-3">{{ $roleLabel }}</td>
                    <td class="px-4 py-3">
                        <x-ui.badge :tone="$user->is_active ? 'success' : 'warning'">
                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                        </x-ui.badge>
                    </td>
                    <td class="px-4 py-3">
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
                    <td colspan="5" class="px-4 py-8 text-center text-secondary-light">No hay usuarios registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>
@endsection
