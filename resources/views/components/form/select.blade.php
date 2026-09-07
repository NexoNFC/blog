@props([
    'name',
    'id' => null,
    'bag' => null,
])

@php
    $id ??= $name;
    $errorBag = $bag ? $errors->{$bag} : $errors;
    $invalid = $errorBag->has($name);
@endphp

<select
    name="{{ $name }}"
    id="{{ $id }}"
    @if ($invalid) aria-invalid="true" @endif
    {{ $attributes->class(['form-control']) }}
>
    {{ $slot }}
</select>
