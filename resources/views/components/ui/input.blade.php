@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'required' => false,
])

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-secondary">
            {{ $label }}
            @if ($required)
                <span class="text-primary">*</span>
            @endif
        </label>
    @endif
    <input
        type="{{ $type }}"
        @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
        @if ($required) required @endif
        @if ($name && $errors->has($name)) aria-invalid="true" @endif
        {{ $attributes->except('class')->class(['form-control']) }}
    >
</div>
