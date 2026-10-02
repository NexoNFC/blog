@props([
    'name',
    'bag' => null,
])

@php
    $errorBag = is_string($bag) && $bag !== '' ? $errors->{$bag} : $errors;
@endphp

@if ($errorBag->has($name))
    <p class="mt-1.5 text-sm font-medium text-danger" role="alert">{{ $errorBag->first($name) }}</p>
@endif
