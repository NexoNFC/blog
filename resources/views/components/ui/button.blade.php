@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'size' => 'md',
])

@php
    $classes = match ($variant) {
        'secondary' => 'border border-white/60 bg-white/55 text-secondary backdrop-blur-md hover:border-secondary/30 hover:bg-white/80',
        'glass' => 'border border-white/30 bg-white/5 text-white backdrop-blur-md hover:bg-white/10',
        'ghost' => 'text-secondary hover:bg-white/50',
        'danger' => 'bg-danger text-white shadow-sm hover:bg-danger/90',
        default => 'bg-primary text-white shadow-sm hover:bg-primary-dark',
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'lg' => 'px-5 py-3 text-base',
        default => 'px-4 py-2.5 text-sm',
    };

    $base = "inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 {$sizeClasses} {$classes}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $base]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $base]) }}>
        {{ $slot }}
    </button>
@endif
