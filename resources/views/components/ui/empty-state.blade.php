@props([
    'title' => 'No hay información para mostrar',
    'description' => null,
    'embedded' => false,
])

<div {{ $attributes->merge(['class' => $embedded
    ? 'px-2 py-6 text-center'
    : 'glass-panel rounded-2xl border-dashed px-6 py-10 text-center'
]) }}>
    <p class="font-semibold text-secondary">{{ $title }}</p>
    @if ($description)
        <p class="mt-2 text-sm text-secondary-light">{{ $description }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-4 flex justify-center">{{ $slot }}</div>
    @endif
</div>
