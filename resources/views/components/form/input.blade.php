@props([
    'type' => 'text',
    'name',
    'id' => null,
    'value' => null,
    'bag' => null,
])

@php
    $id ??= $name;
    $inputValue = $type === 'password' ? '' : ($value ?? old($name));
    $errorBag = $bag ? $errors->{$bag} : $errors;
    $invalid = $errorBag->has($name);
@endphp

<input
    type="{{ $type }}"
    name="{{ $name }}"
    id="{{ $id }}"
    @if ($type !== 'password' || $inputValue !== '') value="{{ $inputValue }}" @endif
    @if ($invalid) aria-invalid="true" @endif
    {{ $attributes->class(['form-control']) }}
>
