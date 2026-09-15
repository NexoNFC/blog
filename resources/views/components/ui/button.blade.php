@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'size' => 'md',
])

@php
    $classes = match ($variant) {
        'secondary' => 'border border-secondary/15 bg-white text-secondary shadow-sm hover:border-secondary/25 hover:bg-secondary/5',
        'glass' => 'border border-white/30 bg-white/5 text-white backdrop-blur-md hover:bg-white/10',
        'ghost' => 'text-secondary hover:bg-white/50',
        'success' => 'bg-success text-white shadow-sm hover:bg-success/90 focus-visible:outline-success',
        'danger' => 'bg-danger text-white shadow-sm hover:bg-danger/90 focus-visible:outline-danger',
        default => 'bg-primary text-white shadow-[0_10px_22px_rgb(200_16_46_/_0.22)] hover:bg-primary-dark',
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-3.5 py-2 text-xs',
        'lg' => 'px-6 py-3 text-base',
        default => 'px-5 py-2.5 text-sm',
    };

    $base = "inline-flex items-center justify-center gap-2 rounded-2xl font-bold tracking-tight transition duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 {$sizeClasses} {$classes}";
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
