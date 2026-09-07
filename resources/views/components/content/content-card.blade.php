@props([
    'content',
    'featured' => false,
])

@php
    $isExternal = ($content['type'] ?? null) === 'externo' || ! empty($content['external_url']);
@endphp

<article {{ $attributes->merge(['class' => $featured
    ? 'flex flex-col'
    : 'flex flex-col glass-panel rounded-2xl p-4 sm:p-5'
]) }}>
    @if ($featured)
        <div class="aspect-[16/9] rounded bg-muted">
            <div class="flex h-full items-end bg-gradient-to-t from-secondary/80 to-transparent p-5">
                <x-ui.badge tone="accent">{{ $content['type'] }}</x-ui.badge>
            </div>
        </div>
    @else
        <x-content.content-meta :content="$content" />
    @endif

    <h3 @class(['mt-4 font-serif font-semibold text-secondary', 'text-2xl sm:text-3xl' => $featured, 'text-lg sm:text-xl' => ! $featured])>
        <a href="{{ route('contents.show', $content['slug']) }}" class="hover:text-primary">
            {{ $content['title'] }}
        </a>
    </h3>

    <p @class(['mt-2 text-secondary-light', 'text-base' => $featured, 'text-sm' => ! $featured])>
        {{ $content['summary'] }}
    </p>

    <div class="mt-4 flex flex-wrap gap-2">
        <x-ui.button
            href="{{ route('contents.show', $content['slug']) }}"
            :variant="$featured ? 'ghost' : 'secondary'"
            @class(['!px-0' => $featured])
        >
            {{ $featured ? 'Leer más' : 'Ver detalle' }}
        </x-ui.button>

        @if ($isExternal && ! empty($content['external_url']))
            <x-content.external-source :url="$content['external_url']" />
        @endif
    </div>
</article>
