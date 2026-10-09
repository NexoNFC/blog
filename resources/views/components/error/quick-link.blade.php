@props([
    'href',
    'icon',
    'label',
])

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'flex flex-col items-center gap-2 rounded-2xl border border-primary/10 bg-white/70 p-4 text-center shadow-sm backdrop-blur-md transition hover:border-primary/20 hover:bg-primary-soft/40',
    ]) }}
>
    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary-soft text-primary">
        <x-icon :name="$icon" class="h-5 w-5" />
    </span>
    <span class="text-sm font-semibold text-secondary">{{ $label }}</span>
</a>
