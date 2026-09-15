@props([
    'point',
])

<div {{ $attributes->merge(['class' => 'reveal glass-card space-y-6 px-5 py-6 sm:px-6']) }}>
    <div class="nfc-point-connection -mx-1" data-nfc-connection>
        <x-nfc.signal class="h-24 w-24 shrink-0" compact />
        <div class="min-w-0">
            <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-success">
                <span class="nfc-status-dot" aria-hidden="true"></span>
                Conexión NFC
            </p>
            <p class="mt-1 text-sm font-medium text-secondary">Punto reconocido correctamente</p>
        </div>
    </div>

    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary">Punto del campus</p>

        <h2 class="mt-3 text-xl font-bold tracking-tight text-secondary sm:text-2xl">
            {{ $point['name'] }}
        </h2>
    </div>

    @if (! empty($point['location']))
        <dl class="space-y-3 border-t border-white/40 pt-5 text-sm">
            <div class="flex items-start justify-between gap-4">
                <dt class="text-secondary-light">Ubicación</dt>
                <dd class="max-w-[60%] text-right font-medium text-secondary">{{ $point['location'] }}</dd>
            </div>
        </dl>
    @endif

    @if (! empty($point['description']))
        <div class="border-t border-white/40 pt-5">
            <p class="text-sm leading-relaxed text-secondary-light">{{ $point['description'] }}</p>
        </div>
    @endif

    <div class="border-t border-white/40 pt-5">
        <x-ui.button href="{{ route('home') }}#nfc" variant="secondary" class="w-full justify-center">
            Ver más puntos del campus
        </x-ui.button>
    </div>
</div>
