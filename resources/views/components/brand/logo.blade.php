@props([
    'href' => null,
    'variant' => 'dark',
])

@php
    $classes = $variant === 'light'
        ? 'text-white'
        : 'text-secondary';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center gap-3 {$classes}"]) }}>
        @if ($variant === 'light')
            <img src="{{ asset('images/brand/logo-fesc-blanco.png') }}" alt="FESC" class="h-10 w-auto">
        @else
            <span class="font-serif text-2xl font-bold tracking-tight">FESC</span>
        @endif
        <span class="sr-only">Fundación de Estudios Superiores Comfanorte</span>
    </a>
@else
    <div {{ $attributes->merge(['class' => "inline-flex items-center gap-3 {$classes}"]) }}>
        @if ($variant === 'light')
            <img src="{{ asset('images/brand/logo-fesc-blanco.png') }}" alt="FESC" class="h-10 w-auto">
        @else
            <span class="font-serif text-2xl font-bold tracking-tight">FESC</span>
        @endif
    </div>
@endif
