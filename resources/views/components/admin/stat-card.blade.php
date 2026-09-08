@props([
    'label',
    'value',
])

<div {{ $attributes->merge(['class' => 'glass-panel rounded-2xl p-4']) }}>
    <p class="text-xs font-semibold uppercase tracking-wide text-secondary-light">{{ $label }}</p>
    <p class="mt-2 font-serif text-3xl font-bold text-secondary">{{ $value }}</p>
</div>
