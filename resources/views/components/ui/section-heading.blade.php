@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'align' => 'left',
])

@php
    $alignClass = $align === 'center' ? 'mx-auto text-center' : '';
@endphp

<div {{ $attributes->merge(['class' => "max-w-2xl {$alignClass}"]) }}>
    @if ($eyebrow)
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-primary">{{ $eyebrow }}</p>
    @endif
    <h2 class="mt-3 font-serif text-3xl font-bold text-secondary sm:text-4xl">{{ $title }}</h2>
    @if ($description)
        <p class="mt-3 text-base leading-relaxed text-secondary-light sm:text-lg">{{ $description }}</p>
    @endif
</div>
