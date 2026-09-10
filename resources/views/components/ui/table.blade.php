@props([
    'inset' => false,
    'wide' => false,
])

<div {{ $attributes->merge(['class' => $inset
    ? 'relative overflow-hidden rounded-xl border border-muted bg-surface/80'
    : 'glass-panel relative overflow-hidden rounded-2xl shadow-sm'
]) }}>
    @isset($header)
        <div class="data-table-header">
            {{ $header }}
        </div>
    @endisset

    <div class="relative overflow-x-auto">
        <table class="data-table rtl:text-right {{ $wide ? 'min-w-[72rem]' : '' }}">
            {{ $slot }}
        </table>
    </div>
</div>
