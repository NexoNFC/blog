<x-guest-layout title="Restablecer contraseña">
    <div class="glass-panel rounded-2xl p-6 shadow-[0_18px_50px_rgb(26_26_27_/_0.12)] sm:p-8">
        <h1 class="font-serif text-2xl font-bold text-secondary">Restablecer contraseña</h1>
        <p class="mt-1 mb-6 text-sm text-secondary-light">
            Ingresa tu correo electrónico y te enviaremos un enlace para definir una nueva contraseña.
        </p>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5" novalidate>
            @csrf

            <div>
                <x-form.label for="email" required>Correo electrónico</x-form.label>
                <x-form.input id="email" name="email" type="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <x-ui.button type="submit" class="w-full">Enviar enlace</x-ui.button>
        </form>
    </div>
</x-guest-layout>
