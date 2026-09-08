<section>
    <header>
        <h2 class="font-serif text-lg font-semibold text-secondary">Actualizar contraseña</h2>
        <p class="mt-1 text-sm text-secondary-light">Usa una contraseña larga y difícil de adivinar.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5" novalidate>
        @csrf
        @method('put')

        <div>
            <x-form.label for="update_password_current_password" required>Contraseña actual</x-form.label>
            <x-form.password id="update_password_current_password" name="current_password" bag="updatePassword" autocomplete="current-password" />
        </div>

        <div>
            <x-form.label for="update_password_password" required>Nueva contraseña</x-form.label>
            <x-form.password id="update_password_password" name="password" bag="updatePassword" autocomplete="new-password" />
        </div>

        <div>
            <x-form.label for="update_password_password_confirmation" required>Confirmar contraseña</x-form.label>
            <x-form.password id="update_password_password_confirmation" name="password_confirmation" bag="updatePassword" autocomplete="new-password" />
        </div>

        <x-ui.button type="submit">Guardar</x-ui.button>
    </form>
</section>
