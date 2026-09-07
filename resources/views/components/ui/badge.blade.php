@props([
    'tone' => 'neutral',
])

@php
    $classes = match ($tone) {
        'success' => 'border-success/20 bg-success-soft/80 text-success',
        'warning' => 'border-warning/20 bg-warning-soft/80 text-warning',
        'danger' => 'border-danger/20 bg-danger-soft/80 text-danger',
        'info' => 'border-info/20 bg-info-soft/80 text-info',
        'accent' => 'border-primary/20 bg-primary text-white',
        default => 'border-white/50 bg-white/60 text-secondary',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-lg border px-2 py-0.5 text-xs font-semibold uppercase tracking-wide backdrop-blur-sm {$classes}"]) }}>
    {{ $slot }}
</span>
