@props([
    'content' => '',
    'placement' => 'top',
    'delay' => 140,
])

@php
    $content = trim((string) $content);
@endphp

<div
    {{ $attributes->class(['ui-tooltip']) }}
    x-data="uiTooltip({
        content: @js($content),
        placement: @js($placement),
        delay: {{ (int) $delay }},
    })"
    @mouseenter="scheduleShow()"
    @mouseleave="scheduleHide()"
    @focusin="scheduleShow()"
    @focusout="scheduleHide()"
>
    {{ $slot }}

    <template x-teleport="body">
        <div
            x-ref="bubble"
            x-cloak
            x-show="visible && Boolean(content)"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1 scale-[0.98]"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-1 scale-[0.98]"
            class="ui-tooltip__bubble"
            :data-placement="placement"
            :style="style"
            role="tooltip"
        >
            <span class="ui-tooltip__accent" aria-hidden="true"></span>
            <span class="ui-tooltip__brand" aria-hidden="true">FESC</span>
            <p class="ui-tooltip__text" x-text="content"></p>
        </div>
    </template>
</div>
