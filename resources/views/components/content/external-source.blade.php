@props([
    'url',
    'label' => 'Ver información oficial',
])

<a
    href="{{ $url }}"
    target="_blank"
    rel="noopener noreferrer"
    {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded border border-muted bg-surface px-4 py-2.5 text-sm font-semibold text-secondary transition hover:border-primary hover:text-primary']) }}
>
    {{ $label }}
</a>
