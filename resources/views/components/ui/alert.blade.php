@props([
    'type' => 'info',
])

@php
    $classes = match ($type) {
        'success' => 'border-success/20 bg-success-soft text-success',
        'warning' => 'border-warning/20 bg-warning-soft text-warning',
        'error' => 'border-danger/20 bg-danger-soft text-danger',
        default => 'border-muted bg-surface text-secondary',
    };
@endphp

<div {{ $attributes->merge(['class' => "rounded border px-4 py-3 text-sm {$classes}", 'role' => 'status']) }}>
    {{ $slot }}
</div>
