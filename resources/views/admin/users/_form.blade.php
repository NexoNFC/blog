@php
    $selectedActive = old('is_active', $user->is_active ? '1' : '0');
    $lockPrivileges = $isLastActiveAdministrator ?? false;
@endphp

@if ($lockPrivileges)
    <x-ui.alert type="warning" class="mb-5" title="Último administrador activo">
        Este usuario es el último administrador activo. Puedes actualizar sus datos, pero no desactivar la cuenta ni quitarle el rol de administrador.
    </x-ui.alert>
    <input type="hidden" name="role" value="admin">
    <input type="hidden" name="is_active" value="1">
@endif

<div>
    <x-form.label for="name" required>Nombre</x-form.label>
    <x-form.input id="name" name="name" :value="old('name', $user->name)" required autocomplete="name" />
</div>

<div>
    <x-form.label for="email" required>Correo electrónico</x-form.label>
    <x-form.input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
</div>

<div>
    <x-form.label for="password">Nueva contraseña</x-form.label>
            <x-form.password id="password" name="password" autocomplete="new-password" />
    <p class="mt-1 text-xs text-secondary-light">Déjala en blanco si no deseas cambiarla.</p>
</div>

<div>
    <x-form.label for="password_confirmation">Confirmar contraseña</x-form.label>
            <x-form.password id="password_confirmation" name="password_confirmation" autocomplete="new-password" />
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
    <x-form.select id="is_active" name="is_active" required @disabled($lockPrivileges)>
        <option value="1" @selected((string) $selectedActive === '1')>Activo</option>
        <option value="0" @selected((string) $selectedActive === '0')>Inactivo</option>
    </x-form.select>
</div>
