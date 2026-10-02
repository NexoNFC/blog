@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
])

@php
    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth];
@endphp

<div
    x-data="{
        show: @js($show),
        focusables() {
            const root = this.$refs.modalRoot;

            if (! (root instanceof HTMLElement)) {
                return [];
            }

            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])';

            return [...root.querySelectorAll(selector)]
                .filter(el => ! el.hasAttribute('disabled'));
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable()?.focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
            $dispatch('close-modal', '{{ $name }}');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="if (show) show = false"
    x-on:keydown.tab.window="if (show) { $event.preventDefault(); $event.shiftKey ? prevFocusable()?.focus() : nextFocusable()?.focus() }"
>
    <template x-teleport="body">
        <div
            x-ref="modalRoot"
            x-show="show"
            class="fixed inset-0 z-[70] overflow-y-auto"
            style="display: {{ $show ? 'block' : 'none' }};"
            role="dialog"
            aria-modal="true"
        >
            <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">
                <div
                    x-show="show"
                    class="fixed inset-0 transform transition-all"
                    x-on:click="show = false"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                >
                    <div class="absolute inset-0 bg-secondary/70 backdrop-blur-sm"></div>
                </div>

                <div
                    x-show="show"
                    class="glass-panel relative z-10 w-full overflow-hidden rounded-2xl shadow-[0_18px_50px_rgb(26_26_27_/_0.16)] transform transition-all {{ $maxWidth }}"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>
