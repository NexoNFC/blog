@props([
    'name',
    'id' => null,
    'value' => '1',
    'checked' => false,
])

@php
    $id ??= $name;
@endphp

<label for="{{ $id }}" class="inline-flex cursor-pointer items-center gap-2">
    <input
        id="{{ $id }}"
        type="checkbox"
        name="{{ $name }}"
        value="{{ $value }}"
        @checked((string) old($name, $checked ? $value : null) === (string) $value)
        {{ $attributes->merge(['class' => 'h-4 w-4 rounded-md border-white/60 bg-white/60 text-primary shadow-sm backdrop-blur-sm transition focus:ring-2 focus:ring-primary/30 disabled:cursor-not-allowed disabled:opacity-50']) }}
    >
    <span class="text-sm text-secondary">{{ $slot }}</span>
</label>
