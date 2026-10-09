@php
    $homeUrl = route('home');
@endphp

<x-layouts.error title="429 · Demasiadas solicitudes — FESC">
    <div class="mx-auto max-w-2xl space-y-5 text-center">
        <p class="text-6xl font-bold tracking-tight text-primary md:text-7xl">429</p>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-secondary-light">Demasiadas solicitudes</p>

        <div class="space-y-2">
            <h1 class="text-xl font-bold tracking-tight text-secondary md:text-2xl">
                Demasiadas solicitudes
            </h1>
            <p class="text-sm leading-relaxed text-secondary-light md:text-base">
                Has realizado varias acciones en poco tiempo. Espera unos segundos antes de intentarlo nuevamente.
            </p>
        </div>

        <div class="flex justify-center pt-1">
            <x-ui.button href="{{ $homeUrl }}" size="lg">Volver al inicio</x-ui.button>
        </div>

        <div class="mx-auto grid max-w-xs grid-cols-1 gap-3 pt-2">
            <x-error.quick-link :href="$homeUrl" icon="home" label="Inicio" />
        </div>

        <p class="pt-2 text-xs text-secondary-light">
            Si sigues viendo este mensaje, contacta al equipo de soporte de la plataforma FESC.
        </p>
    </div>
</x-layouts.error>
