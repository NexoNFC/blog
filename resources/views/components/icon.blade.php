<svg {{ $attributes->merge([
    'class' => 'h-6 w-6',
    'xmlns' => 'http://www.w3.org/2000/svg',
    'fill' => $icon['fill'],
    'viewBox' => $icon['viewBox'],
    'aria-hidden' => 'true',
]) }}>
    {!! $icon['inner'] !!}
</svg>
