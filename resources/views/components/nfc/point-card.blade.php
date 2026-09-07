@props([
    'location',
])

@php
    $href = $location['href'] ?? null;
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'group reveal flex h-full flex-col rounded-2xl border border-muted bg-surface p-5 transition duration-300 hover:-translate-y-1 hover:border-primary/40 hover:shadow-lg']) }}
>
    <div class="flex items-start justify-between gap-3">
        <x-ui.icon :name="$location['icon'] ?? 'building'" />
        @if (! empty($location['badge']))
            <x-ui.badge tone="accent">{{ $location['badge'] }}</x-ui.badge>
        @endif
    </div>

    <h3 class="mt-5 font-serif text-xl font-semibold text-secondary group-hover:text-primary">
        {{ $location['name'] }}
    </h3>
    <p class="mt-2 text-sm text-secondary-light">{{ $location['detail'] }}</p>

    @if ($href)
        <span class="mt-auto pt-5 text-sm font-semibold text-primary">Explorar este punto</span>
    @endif
</{{ $tag }}>
