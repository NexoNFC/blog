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
        {{ $attributes->except('class')->merge(['class' => 'block w-full rounded border border-muted bg-surface px-3 py-2.5 text-sm text-text shadow-sm transition placeholder:text-secondary-light focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20']) }}
    >
</div>
