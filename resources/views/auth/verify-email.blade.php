<x-guest-layout title="Verificar correo">
    <div class="glass-panel rounded-2xl p-6 shadow-[0_18px_50px_rgb(26_26_27_/_0.12)] sm:p-8">
        <h1 class="font-serif text-2xl font-bold text-secondary">Verifica tu correo</h1>
        <p class="mt-2 mb-6 text-sm text-secondary-light">
            Gracias por registrarte. Confirma tu dirección haciendo clic en el enlace que te enviamos.
            Si no llegó, podemos enviar otro.
        </p>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-ui.button type="submit">Reenviar verificación</x-ui.button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-semibold text-secondary-light underline-offset-2 hover:text-secondary hover:underline">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
