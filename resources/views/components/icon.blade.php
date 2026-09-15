@php
    $userClass = trim((string) $attributes->get('class', ''));
    $hasExplicitSize = $userClass !== '' && preg_match('/(?:^|\s)!?(?:h|w|size)-[^\s]+/', $userClass) === 1;
    $class = $hasExplicitSize ? $userClass : trim('h-6 w-6 '.$userClass);
@endphp

<svg {{ $attributes->except('class')->merge([
    'class' => $class,
    'xmlns' => 'http://www.w3.org/2000/svg',
    'fill' => $icon['fill'],
    'viewBox' => $icon['viewBox'],
    'aria-hidden' => 'true',
]) }}>
    {!! $icon['inner'] !!}
</svg>
