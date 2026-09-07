@props([
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'glass-panel rounded-2xl'.($padding ? ' p-5 sm:p-6' : '')]) }}>
    {{ $slot }}
</div>
