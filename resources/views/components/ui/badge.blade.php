@props([
    'tone' => 'neutral',
])

@php
    $classes = match ($tone) {
        'success' => 'admin-badge--success',
        'warning' => 'admin-badge--warning',
        'danger' => 'admin-badge--danger',
        'info' => 'admin-badge--info',
        'accent' => 'admin-badge--accent',
        default => 'admin-badge--neutral',
    };
@endphp

<span {{ $attributes->merge(['class' => "admin-badge {$classes}"]) }}>
    {{ $slot }}
</span>
