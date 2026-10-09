@extends('layouts.public')

@section('title', 'Vista 360° · '.$point['name'])

@section('content')
    <section
        class="tour-shell flex-1"
        x-data="nfcTourViewer({
            imageUrl: @js($point['panorama_url']),
            hotspots: @js($point['hotspots']),
            editMode: false,
            markerLabel: 'Tarjeta NFC',
        })"
    >
        <div class="tour-shell__canvas" x-ref="canvas" aria-label="Vista 360 del punto {{ $point['name'] }}"></div>

        <div class="tour-shell__hud">
            <div class="tour-shell__top">
                <div class="tour-shell__top-copy min-w-0">
                    <p class="tour-shell__eyebrow">Vista 360°</p>
                    <h1 class="tour-shell__title">{{ $point['name'] }}</h1>
                    @if (! empty($point['location']))
                        <p class="tour-shell__location">{{ $point['location'] }}</p>
                    @endif
                </div>

                <div class="tour-shell__actions">
                    <x-ui.button href="{{ $point['info_url'] }}" variant="glass" size="sm">Info NFC</x-ui.button>
                    <x-ui.button href="{{ route('home') }}#campus" variant="glass" size="sm">Campus</x-ui.button>
                </div>
            </div>

            @if (! empty($point['description']))
                <p class="tour-shell__hint">{{ $point['description'] }}</p>
            @endif
        </div>

        <div
            class="tour-shell__panel"
            x-cloak
            x-show="selected"
            x-transition
        >
            <button type="button" class="tour-shell__close" @click="closeSelected()" aria-label="Cerrar detalle">×</button>
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-primary">Ubicación NFC</p>
            <h2 class="mt-2 text-xl font-bold text-secondary" x-text="selected?.title"></h2>
            <p class="mt-2 text-sm leading-relaxed text-secondary-light" x-text="selected?.description"></p>
        </div>

        <p class="tour-shell__help">
            Arrastra para mirar · Usa la rueda para acercar
            @if (! empty($point['marker']))
                · La moneda FESC/NFC marca la tarjeta
            @endif
        </p>
    </section>
@endsection
