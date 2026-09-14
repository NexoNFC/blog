@props([
    'onDark' => false,
    'compact' => false,
])

<div
    aria-hidden="true"
    data-nfc-signal
    {{ $attributes->class([
        'nfc-signal',
        'nfc-signal--dark' => $onDark,
        'nfc-signal--compact' => $compact,
    ]) }}
>
    <span class="nfc-signal__grid"></span>
    <span class="nfc-signal__orbit nfc-signal__orbit--outer"></span>
    <span class="nfc-signal__orbit nfc-signal__orbit--middle"></span>
    <span class="nfc-signal__orbit nfc-signal__orbit--inner"></span>
    <span class="nfc-signal__beam"></span>
    <span class="nfc-signal__core">
        <strong>NFC</strong>
    </span>
    <span class="nfc-signal__node nfc-signal__node--one"></span>
    <span class="nfc-signal__node nfc-signal__node--two"></span>
    <span class="nfc-signal__node nfc-signal__node--three"></span>
</div>
