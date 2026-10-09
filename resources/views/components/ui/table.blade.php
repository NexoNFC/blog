@props([
    'inset' => false,
    'wide' => false,
    'tableClass' => '',
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
        <table @class([
            'data-table rtl:text-right',
            'min-w-[72rem]' => $wide,
            $tableClass => filled($tableClass),
        ])>
            {{ $slot }}
        </table>
    </div>
</div>
