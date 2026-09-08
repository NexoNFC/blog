@extends('layouts.admin')

@section('title', 'Nuevo usuario')
@section('heading', 'Crear usuario')
@section('subtitle', 'El usuario podrá iniciar sesión en el panel administrativo')

@section('actions')
    <x-ui.button href="{{ route('admin.users.index') }}" variant="secondary">Volver</x-ui.button>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.users.store') }}" class="mx-auto max-w-xl space-y-5" novalidate>
        @csrf

        <x-ui.card class="space-y-5">
        <div>
            <x-form.label for="name" required>Nombre</x-form.label>
            <x-form.input id="name" name="name" required autocomplete="name" />
        </div>

        <div>
            <x-form.label for="email" required>Correo electrónico</x-form.label>
            <x-form.input id="email" name="email" type="email" required autocomplete="username" />
        </div>

        <div>
            <x-form.label for="password" required>Contraseña</x-form.label>
            <x-form.password id="password" name="password" required autocomplete="new-password" />
        </div>

        <div>
            <x-form.label for="password_confirmation" required>Confirmar contraseña</x-form.label>
            <x-form.password id="password_confirmation" name="password_confirmation" required autocomplete="new-password" />
        </div>

        <div>
            <input type="hidden" name="role" value="admin">
            <p class="text-sm text-secondary">
                <span class="font-medium">Rol:</span> Administrador
            </p>
            <p class="mt-1 text-xs text-secondary-light">El panel solo admite el rol de administrador.</p>
        </div>

        <div>
            <x-form.label for="is_active" required>Estado</x-form.label>
            <x-form.select id="is_active" name="is_active" required>
                <option value="1" @selected(old('is_active', '1') === '1')>Activo</option>
                <option value="0" @selected((string) old('is_active') === '0')>Inactivo</option>
            </x-form.select>
        </div>

        <div class="flex flex-wrap gap-3 border-t border-white/40 pt-5">
            <x-ui.button type="submit">Guardar usuario</x-ui.button>
            <x-ui.button href="{{ route('admin.users.index') }}" variant="secondary">Cancelar</x-ui.button>
        </div>
        </x-ui.card>
    </form>
@endsection
