@props([
    'title' => 'No hay información para mostrar',
    'description' => null,
    'embedded' => false,
])

<div {{ $attributes->merge(['class' => $embedded
    ? 'admin-empty px-2 py-8 text-center'
    : 'admin-empty glass-panel rounded-2xl border-dashed px-6 py-10 text-center'
]) }}>
    <span class="admin-empty__mark" aria-hidden="true"></span>
    <p class="mt-3 font-bold tracking-tight text-secondary">{{ $title }}</p>
    @if ($description)
        <p class="mt-1.5 text-sm text-secondary-light">{{ $description }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-4 flex justify-center">{{ $slot }}</div>
    @endif
</div>
