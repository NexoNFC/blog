@props([
    'front' => 'FESC',
    'back' => 'NFC',
    'caption' => 'CÚCUTA',
])

<span {{ $attributes->class(['fesc-coin']) }} aria-hidden="true">
    <span class="fesc-coin__layer fesc-coin__layer--back"></span>
    <span class="fesc-coin__layer fesc-coin__layer--back-middle"></span>
    <span class="fesc-coin__layer fesc-coin__layer--middle"></span>
    <span class="fesc-coin__layer fesc-coin__layer--front-middle"></span>
    <span class="fesc-coin__layer fesc-coin__layer--front"></span>
    <span class="fesc-coin__face fesc-coin__face--front">
        <strong>{{ $front }}</strong>
        <small>{{ $caption }}</small>
    </span>
    <span class="fesc-coin__face fesc-coin__face--back">
        <strong>{{ $back }}</strong>
        <small>{{ $caption }}</small>
    </span>
    <span class="fesc-coin__rim"></span>
</span>
