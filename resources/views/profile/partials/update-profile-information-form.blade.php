<section>
    <header>
        <h2 class="font-serif text-lg font-semibold text-secondary">Información del perfil</h2>
        <p class="mt-1 text-sm text-secondary-light">Actualiza tu nombre y correo electrónico.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5" novalidate>
        @csrf
        @method('patch')

        <div>
            <x-form.label for="name" required>Nombre</x-form.label>
            <x-form.input id="name" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
        </div>

        <div>
            <x-form.label for="email" required>Correo electrónico</x-form.label>
            <x-form.input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="mt-2 text-sm text-secondary-light">
                    Tu correo no está verificado.
                    <button form="send-verification" class="font-semibold text-primary underline-offset-2 hover:underline">
                        Reenviar enlace de verificación
                    </button>
                </p>
            @endif
        </div>

        <x-ui.button type="submit">Guardar</x-ui.button>
    </form>
</section>
