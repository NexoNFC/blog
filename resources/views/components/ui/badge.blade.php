@props([
    'tone' => 'neutral',
])

@php
    $classes = match ($tone) {
        'success' => 'bg-success-soft text-success',
        'warning' => 'bg-warning-soft text-warning',
        'danger' => 'bg-danger-soft text-danger',
        'info' => 'bg-info-soft text-info',
        'accent' => 'bg-primary text-white',
        default => 'bg-muted text-secondary',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded px-2 py-0.5 text-xs font-semibold uppercase tracking-wide {$classes}"]) }}>
    {{ $slot }}
</span>
