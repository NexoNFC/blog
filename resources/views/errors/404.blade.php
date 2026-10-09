@php
    $homeUrl = route('home');
    $newsUrl = route('home').'#noticias';
    $campusUrl = route('home').'#campus';
    $nfcUrl = route('home').'#nfc';
@endphp

<x-layouts.error title="404 · Página no encontrada — FESC">
    <div class="mx-auto max-w-2xl space-y-5 text-center">
        <p class="text-6xl font-bold tracking-tight text-primary md:text-7xl">404</p>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-secondary-light">Página no encontrada</p>

        <div class="space-y-2">
            <h1 class="text-xl font-bold tracking-tight text-secondary md:text-2xl">
                Vaya, no encontramos esa página
            </h1>
            <p class="text-sm leading-relaxed text-secondary-light md:text-base">
                La dirección puede haber cambiado, estar deshabilitada o nunca haber existido.
                Revisa la URL o usa alguno de los accesos rápidos.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 md:grid-cols-4">
            <x-error.quick-link :href="$homeUrl" icon="home" label="Inicio" />
            <x-error.quick-link :href="$newsUrl" icon="annotation" label="Noticias" />
            <x-error.quick-link :href="$campusUrl" icon="users" label="Campus" />
            <x-error.quick-link :href="$nfcUrl" icon="nfc" label="NFC" />
        </div>

        <p class="pt-2 text-xs text-secondary-light">
            Si sigues viendo este mensaje, contacta al equipo de soporte de la plataforma FESC.
        </p>
    </div>
</x-layouts.error>
