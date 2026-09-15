@props([
    'onDark' => false,
    'compact' => false,
])

<div
    @if ($attributes->get('aria-hidden') !== null)
        aria-hidden="{{ $attributes->get('aria-hidden') }}"
    @else
        aria-hidden="true"
    @endif
    data-nfc-signal
    {{ $attributes->except('aria-hidden')->class([
        'nfc-signal',
        'nfc-signal--dark' => $onDark,
        'nfc-signal--compact' => $compact,
        'nfc-signal--coin' => ! $slot->isEmpty(),
    ]) }}
>
    <span class="nfc-signal__grid"></span>
    <span class="nfc-signal__orbit nfc-signal__orbit--outer"></span>
    <span class="nfc-signal__orbit nfc-signal__orbit--middle"></span>
    <span class="nfc-signal__orbit nfc-signal__orbit--inner"></span>
    <span class="nfc-signal__beam"></span>
    <span class="nfc-signal__core">
        @if ($slot->isEmpty())
            <strong>NFC</strong>
        @else
            {{ $slot }}
        @endif
    </span>
    <span class="nfc-signal__node nfc-signal__node--one"></span>
    <span class="nfc-signal__node nfc-signal__node--two"></span>
    <span class="nfc-signal__node nfc-signal__node--three"></span>
</div>
