@php
    $homeUrl = route('home');
@endphp

<x-layouts.error title="503 · Mantenimiento — FESC">
    <div class="mx-auto max-w-2xl space-y-5 text-center">
        <p class="text-6xl font-bold tracking-tight text-primary md:text-7xl">503</p>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-secondary-light">Mantenimiento</p>

        <div class="space-y-2">
            <h1 class="text-xl font-bold tracking-tight text-secondary md:text-2xl">
                Plataforma en mantenimiento
            </h1>
            <p class="text-sm leading-relaxed text-secondary-light md:text-base">
                Estamos realizando mejoras en la plataforma informativa FESC.
                Volveremos en línea en breve. Gracias por tu paciencia.
            </p>
        </div>

        <div class="flex justify-center pt-1">
            <x-ui.button href="{{ $homeUrl }}" size="lg">Volver al inicio</x-ui.button>
        </div>

        <div class="mx-auto grid max-w-xs grid-cols-1 gap-3 pt-2">
            <x-error.quick-link :href="$homeUrl" icon="home" label="Inicio" />
        </div>

        <p class="pt-2 text-xs text-secondary-light">
            El mantenimiento suele durar pocos minutos. Si la plataforma no vuelve pronto, contacta al equipo de soporte.
        </p>
    </div>
</x-layouts.error>
