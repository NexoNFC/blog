@php
    $homeUrl = route('home');
    $nfcUrl = route('home').'#nfc';
@endphp

<x-layouts.error title="500 · Error interno — FESC">
    <div class="mx-auto max-w-2xl space-y-5 text-center">
        <p class="text-6xl font-bold tracking-tight text-primary md:text-7xl">500</p>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-secondary-light">Error del servidor</p>

        <div class="space-y-2">
            <h1 class="text-xl font-bold tracking-tight text-secondary md:text-2xl">
                Error interno del servidor
            </h1>
            <p class="text-sm leading-relaxed text-secondary-light md:text-base">
                Ocurrió un problema inesperado al procesar tu solicitud.
                Intenta de nuevo en unos minutos.
            </p>
        </div>

        <div class="mx-auto grid max-w-md grid-cols-2 gap-3 pt-2">
            <x-error.quick-link :href="$homeUrl" icon="home" label="Inicio" />
            <x-error.quick-link :href="$nfcUrl" icon="nfc" label="NFC" />
        </div>

        <p class="pt-2 text-xs text-secondary-light">
            Si el problema persiste, contacta al equipo de soporte e indica la hora en que ocurrió el error.
        </p>
    </div>
</x-layouts.error>
