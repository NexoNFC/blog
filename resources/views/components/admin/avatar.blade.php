@props([
    'user',
    'size' => 'md',
])

@php
    $classes = match ($size) {
        'lg' => 'h-20 w-20 text-2xl',
        'sm' => 'h-8 w-8 text-xs',
        default => 'h-10 w-10 text-sm',
    };
@endphp

@if ($user->avatarUrl())
    <img
        src="{{ $user->avatarUrl() }}"
        alt="Foto de {{ $user->name }}"
        {{ $attributes->class(['admin-avatar object-cover', $classes]) }}
    >
@else
    <span {{ $attributes->class(['admin-avatar', $classes]) }}>
        {{ $user->initial() }}
    </span>
@endif
