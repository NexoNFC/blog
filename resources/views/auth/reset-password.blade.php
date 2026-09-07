<x-guest-layout title="Nueva contraseña">
    <div class="glass-panel rounded-2xl p-6 shadow-[0_18px_50px_rgb(26_26_27_/_0.12)] sm:p-8">
        <h1 class="font-serif text-2xl font-bold text-secondary">Nueva contraseña</h1>
        <p class="mt-1 mb-6 text-sm text-secondary-light">Define una contraseña para tu cuenta administrativa.</p>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5" novalidate>
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-form.label for="email" required>Correo electrónico</x-form.label>
                <x-form.input id="email" name="email" type="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            </div>

            <div>
                <x-form.label for="password" required>Contraseña</x-form.label>
                <x-form.password id="password" name="password" required autocomplete="new-password" />
            </div>

            <div>
                <x-form.label for="password_confirmation" required>Confirmar contraseña</x-form.label>
                <x-form.password id="password_confirmation" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <x-ui.button type="submit" class="w-full">Guardar contraseña</x-ui.button>
        </form>
    </div>
</x-guest-layout>
