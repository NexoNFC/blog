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

<select
    name="{{ $name }}"
    id="{{ $id }}"
    @if ($invalid) aria-invalid="true" @endif
    {{ $attributes->class(['form-select']) }}
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
