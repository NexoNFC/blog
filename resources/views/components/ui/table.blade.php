@props([
    'inset' => false,
    'wide' => false,
])

<div {{ $attributes->merge(['class' => $inset
    ? 'admin-table-shell admin-table-shell--inset'
    : 'admin-table-shell glass-panel'
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
