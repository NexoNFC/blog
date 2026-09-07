@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between']) }}>
    <div>
        <h1 class="text-xl font-semibold text-secondary sm:text-2xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-0.5 text-sm text-secondary-light">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="shrink-0">{{ $slot }}</div>
    @endif
</div>
