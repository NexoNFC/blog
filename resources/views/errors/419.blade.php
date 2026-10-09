@php
    $homeUrl = route('home');
@endphp

<x-layouts.error title="419 · Sesión expirada — FESC">
    <div class="mx-auto max-w-2xl space-y-5 text-center">
        <span class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-soft text-primary">
            <x-icon name="arrows-repeat" class="h-8 w-8" />
        </span>

        <div class="space-y-2">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-secondary-light">Sesión expirada</p>
            <h1 class="text-xl font-bold tracking-tight text-secondary md:text-2xl">
                Tu sesión ha caducado
            </h1>
            <p class="text-sm leading-relaxed text-secondary-light md:text-base">
                Por seguridad, el formulario o la sesión ya no es válido. Recarga la página e inténtalo de nuevo.
            </p>
        </div>

        <div class="flex flex-wrap justify-center gap-3 pt-1">
            <x-ui.button type="button" size="lg" onclick="window.location.reload()">
                Recargar página
            </x-ui.button>
            <x-ui.button href="{{ $homeUrl }}" variant="secondary" size="lg">
                Volver al inicio
            </x-ui.button>
        </div>

        <p class="pt-2 text-xs text-secondary-light">
            Si el problema continúa, contacta al equipo de soporte de la plataforma FESC.
        </p>
    </div>
</x-layouts.error>
