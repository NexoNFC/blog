@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-2.5 block text-sm font-medium text-text']) }}>
    {{ $value ?? $slot }}
</label>
