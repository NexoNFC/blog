@props([
    'point',
])

<section {{ $attributes->merge(['class' => 'border-b border-muted bg-surface']) }}>
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10">
        <div class="flex flex-wrap items-center gap-2">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary">Punto NFC</p>
            <x-nfc.point-status :status="$point['status']" />
        </div>

        <h1 class="mt-3 text-3xl font-bold sm:text-4xl">{{ $point['name'] }}</h1>

        <p class="mt-2 text-sm text-secondary-light sm:text-base">
            {{ $point['location'] }} · {{ $point['identifier'] }}
        </p>

        @if (! empty($point['description']))
            <p class="mt-4 max-w-2xl text-secondary">{{ $point['description'] }}</p>
        @endif

        <p class="mt-4 text-sm font-medium text-secondary">
            Has llegado desde este punto del campus.
        </p>
    </div>
</section>
