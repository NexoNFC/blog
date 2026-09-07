@props([
    'for',
    'required' => false,
])

<label for="{{ $for }}" {{ $attributes->merge(['class' => 'mb-2 block text-sm font-bold text-secondary']) }}>
    {{ $slot }}
    @if ($required)
        <span class="text-primary" aria-hidden="true">*</span>
    @endif
</label>
