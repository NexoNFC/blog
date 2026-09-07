@props([
    'title' => 'No hay información para mostrar',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'rounded border border-dashed border-muted bg-surface px-6 py-10 text-center']) }}>
    <p class="font-semibold text-secondary">{{ $title }}</p>
    @if ($description)
        <p class="mt-2 text-sm text-secondary-light">{{ $description }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-4 flex justify-center">{{ $slot }}</div>
    @endif
</div>
