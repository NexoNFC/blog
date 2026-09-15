@props([
    'label',
    'value',
    'hint' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'admin-stat-card glass-hover glass-panel']) }}>
    <div class="flex items-start justify-between gap-3">
        <p class="admin-stat-card__label">{{ $label }}</p>
        @if ($icon)
            <span class="admin-stat-card__icon">
                <x-icon :name="$icon" class="h-4 w-4" />
            </span>
        @endif
    </div>
    <p class="admin-stat-card__value">{{ $value }}</p>
    @if ($hint)
        <p class="admin-stat-card__hint">{{ $hint }}</p>
    @endif
</div>
