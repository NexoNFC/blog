@props([
    'location',
])

@php
    $href = $location['href'] ?? null;
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'group glass-card glass-hover reveal flex h-full flex-col overflow-hidden']) }}
>
    @if (! empty($location['image']))
        <div class="overflow-hidden">
            <img
                src="{{ asset($location['image']) }}"
                alt="{{ $location['name'] }}"
                class="h-36 w-full object-cover transition duration-300 group-hover:scale-105"
                loading="lazy"
            >
        </div>
    @endif

    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-start justify-between gap-3">
            <x-ui.icon :name="$location['icon'] ?? 'bookmark'" />
            @if (! empty($location['badge']))
                <x-ui.badge tone="accent">{{ $location['badge'] }}</x-ui.badge>
            @endif
        </div>

        <h3 class="mt-5 text-xl font-semibold text-secondary group-hover:text-primary">
            {{ $location['name'] }}
        </h3>
        <p class="mt-2 text-sm text-secondary-light">{{ $location['detail'] }}</p>

        @if ($href)
            <span class="mt-auto pt-5 text-sm font-semibold text-primary">Explorar este punto</span>
        @endif
    </div>
</{{ $tag }}>
