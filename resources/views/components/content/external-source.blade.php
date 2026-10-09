@props([
    'url',
    'label' => 'Ver información oficial',
])

<x-ui.button
    :href="$url"
    variant="secondary"
    target="_blank"
    rel="noopener noreferrer"
    {{ $attributes }}
>
    {{ $label }}
</x-ui.button>
