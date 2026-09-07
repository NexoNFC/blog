<x-guest-layout title="Confirmar contraseña">
    <div class="glass-panel rounded-2xl p-6 shadow-[0_18px_50px_rgb(26_26_27_/_0.12)] sm:p-8">
        <h1 class="font-serif text-2xl font-bold text-secondary">Confirmar contraseña</h1>
        <p class="mt-1 mb-6 text-sm text-secondary-light">
            Esta es una zona segura. Confirma tu contraseña para continuar.
        </p>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5" novalidate>
            @csrf

            <div>
                <x-form.label for="password" required>Contraseña</x-form.label>
                <x-form.password id="password" name="password" required autocomplete="current-password" />
            </div>

            <x-ui.button type="submit" class="w-full">Confirmar</x-ui.button>
        </form>
    </div>
</x-guest-layout>
