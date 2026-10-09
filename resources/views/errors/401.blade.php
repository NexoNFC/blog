@php
    $homeUrl = route('home');
    $loginUrl = route('login');
@endphp

<x-layouts.error title="401 · No autenticado — FESC">
    <div class="mx-auto max-w-2xl space-y-5 text-center">
        <p class="text-6xl font-bold tracking-tight text-primary md:text-7xl">401</p>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-secondary-light">No autenticado</p>

        <div class="space-y-2">
            <h1 class="text-xl font-bold tracking-tight text-secondary md:text-2xl">
                Necesitas iniciar sesión
            </h1>
            <p class="text-sm leading-relaxed text-secondary-light md:text-base">
                Debes autenticarte para acceder a esta sección administrativa de la plataforma.
            </p>
        </div>

        <div class="flex flex-wrap justify-center gap-3 pt-1">
            <x-ui.button href="{{ $loginUrl }}" size="lg">Ir al inicio de sesión</x-ui.button>
            <x-ui.button href="{{ $homeUrl }}" variant="secondary" size="lg">Ir al inicio</x-ui.button>
        </div>

        <div class="mx-auto grid max-w-md grid-cols-2 gap-3 pt-2">
            <x-error.quick-link :href="$loginUrl" icon="login" label="Iniciar sesión" />
            <x-error.quick-link :href="$homeUrl" icon="home" label="Inicio" />
        </div>

        <p class="pt-2 text-xs text-secondary-light">
            Si sigues viendo este mensaje, contacta al equipo de soporte de la plataforma FESC.
        </p>
    </div>
</x-layouts.error>
