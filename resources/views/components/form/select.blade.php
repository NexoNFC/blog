@props([
    'name',
    'id' => null,
    'bag' => null,
    'options' => null,
    'selected' => null,
])

@php
    $id ??= $name;
    $errorBag = $bag ? $errors->{$bag} : $errors;
    $invalid = $errorBag->has($name);

    $normalizedOptions = null;

    if (is_iterable($options)) {
        $normalizedOptions = collect($options)
            ->map(function (mixed $option): array {
                if (is_array($option)) {
                    return [
                        'value' => (string) ($option['value'] ?? ''),
                        'label' => (string) ($option['label'] ?? $option['value'] ?? ''),
                    ];
                }

                return [
                    'value' => (string) $option,
                    'label' => (string) $option,
                ];
            })
            ->values()
            ->all();
    }

    $current = old($name, $selected);
    $current = $current === null ? '' : (string) $current;
@endphp

<div
    x-data="fancySelect"
    {{ $attributes->except(['disabled', 'required'])->class(['fancy-select relative min-w-0']) }}
    @keydown.escape.window="close()"
>
    <button
        type="button"
        x-ref="trigger"
        class="fancy-select__trigger"
        @click.stop="toggle()"
        @keydown.arrow-down.prevent="open = true; $nextTick(() => positionMenu())"
        :aria-expanded="open.toString()"
        aria-haspopup="listbox"
        :disabled="disabled"
        @if ($invalid) aria-invalid="true" @endif
    >
        <span class="fancy-select__label" x-text="label">Seleccionar</span>
        <span class="fancy-select__chevron" aria-hidden="true">
            <svg viewBox="0 0 12 8" fill="none" class="h-2.5 w-3">
                <path d="M1.5 1.75 6 6.25 10.5 1.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
    </button>

    <template x-teleport="body">
        <div
            x-ref="menu"
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="fancy-select__menu"
            :style="menuStyle"
            role="listbox"
        >
            <template x-for="option in options" :key="option.value + '-' + option.label">
                <button
                    type="button"
                    class="fancy-select__option"
                    role="option"
                    :aria-selected="(option.value === value).toString()"
                    :class="{
                        'is-selected': option.value === value,
                        'is-disabled': option.disabled,
                    }"
                    :disabled="option.disabled"
                    @click="choose(option)"
                    x-text="option.label"
                ></button>
            </template>
        </div>
    </template>

    <select
        x-ref="select"
        name="{{ $name }}"
        id="{{ $id }}"
        class="sr-only"
        tabindex="-1"
        aria-hidden="true"
        @if ($invalid) aria-invalid="true" @endif
        {{ $attributes->only(['disabled', 'required']) }}
    >
        @if ($normalizedOptions !== null)
            @foreach ($normalizedOptions as $option)
                <option
                    value="{{ $option['value'] }}"
                    @selected($option['value'] === $current)
                    title="{{ $option['label'] }}"
                >
                    {{ $option['label'] }}
                </option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>
</div>
