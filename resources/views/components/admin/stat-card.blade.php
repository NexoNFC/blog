@props([
    'label',
    'value',
])

<div {{ $attributes->merge(['class' => 'rounded border border-muted bg-surface p-4']) }}>
    <p class="text-xs font-semibold uppercase tracking-wide text-secondary-light">{{ $label }}</p>
    <p class="mt-2 font-serif text-3xl font-bold text-secondary">{{ $value }}</p>
</div>
