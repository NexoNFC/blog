@php
    $homeUrl = route('home');
    $newsUrl = route('home').'#noticias';
@endphp

<x-layouts.error title="Noticia no publicada — FESC">
    <div class="mx-auto max-w-2xl space-y-5 text-center">
        <span class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-warning-soft text-warning">
            <x-icon name="annotation" class="h-8 w-8" />
        </span>

        <div class="space-y-2">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-secondary-light">Aún no publicada</p>
            <h1 class="text-xl font-bold tracking-tight text-secondary md:text-2xl">
                Esta noticia todavía no es pública
            </h1>
            <p class="text-sm leading-relaxed text-secondary-light md:text-base">
                @if (! empty($title))
                    «{{ $title }}» está en borrador o archivada.
                @else
                    La pieza existe en el catálogo, pero aún no fue aprobada.
                @endif
                Un administrador debe publicarla desde el panel para que aparezca aquí.
            </p>
        </div>

        <div class="flex flex-wrap justify-center gap-3 pt-1">
            <x-ui.button href="{{ $homeUrl }}" size="lg">Volver al inicio</x-ui.button>
            <x-ui.button href="{{ $newsUrl }}" variant="secondary" size="lg">Ver noticias</x-ui.button>
        </div>
    </div>
</x-layouts.error>
