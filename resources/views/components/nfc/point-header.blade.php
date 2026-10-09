@props([
    'point',
])

<div {{ $attributes->merge(['class' => 'reveal glass-card space-y-4 px-4 py-4 sm:px-5']) }}>
    <div class="nfc-point-connection -mx-0.5" data-nfc-connection>
        <div class="nfc-point-connection__mark" aria-hidden="true">
            <x-nfc.coin class="nfc-point-connection__coin" front="FESC" back="NFC" caption="NFC" />
        </div>
        <div class="min-w-0">
            <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-success">
                <span class="nfc-status-dot" aria-hidden="true"></span>
                Conexión NFC
            </p>
            <p class="mt-0.5 text-sm font-medium text-secondary">Punto reconocido</p>
        </div>
    </div>

    <div>
        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-primary">Punto del campus</p>
        <h2 class="mt-1 text-lg font-bold tracking-tight text-secondary sm:text-xl">
            {{ $point['name'] }}
        </h2>

        @if (! empty($point['location']) || ! empty($point['description']))
            <div class="mt-2.5 space-y-1.5 text-sm leading-snug text-secondary-light">
                @if (! empty($point['location']))
                    <p>
                        <span class="font-medium text-secondary">Ubicación:</span>
                        {{ $point['location'] }}
                    </p>
                @endif
                @if (! empty($point['description']))
                    <p>{{ $point['description'] }}</p>
                @endif
            </div>
        @endif
    </div>

    <div class="space-y-2 border-t border-white/40 pt-3.5">
        @if (! empty($point['has_tour']) && ! empty($point['tour_url']))
            <x-ui.button href="{{ $point['tour_url'] }}" class="w-full justify-center">
                Explorar en 360°
            </x-ui.button>
        @endif
        <x-ui.button href="{{ route('home') }}#nfc" variant="secondary" class="w-full justify-center">
            Ver más puntos del campus
        </x-ui.button>
    </div>
</div>
