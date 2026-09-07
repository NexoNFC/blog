<section class="space-y-6">
    <header>
        <h2 class="font-serif text-lg font-semibold text-secondary">Eliminar cuenta</h2>
        <p class="mt-1 text-sm text-secondary-light">
            Al eliminar la cuenta se perderán sus datos de forma permanente.
        </p>
    </header>

    <x-ui.button
        variant="danger"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        Eliminar cuenta
    </x-ui.button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6" novalidate>
            @csrf
            @method('delete')

            <h2 class="font-serif text-lg font-semibold text-secondary">¿Eliminar esta cuenta?</h2>
            <p class="mt-1 text-sm text-secondary-light">
                Escribe tu contraseña para confirmar que deseas eliminar la cuenta de forma permanente.
            </p>

            <div class="mt-6">
                <x-form.label for="password" class="sr-only">Contraseña</x-form.label>
                <x-form.password id="password" name="password" bag="userDeletion" autocomplete="current-password" placeholder="Contraseña" class="w-3/4" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-ui.button type="button" variant="secondary" x-on:click="$dispatch('close')">
                    Cancelar
                </x-ui.button>
                <x-ui.button type="submit" variant="danger">
                    Eliminar cuenta
                </x-ui.button>
            </div>
        </form>
    </x-modal>
</section>
