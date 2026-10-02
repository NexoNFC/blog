@props([
    'content',
    'image' => null,
])

@php
    $isExternal = ($content['type'] ?? null) === 'externo' || ! empty($content['external_url']);
    $imageSrc = $image ?? ($content['image'] ?? null);
@endphp

<article {{ $attributes->merge([
    'class' => 'group glass-card glass-hover reveal flex h-full w-full min-w-0 flex-col overflow-hidden',
]) }}>
    <a href="{{ route('contents.show', $content['slug']) }}" class="relative block overflow-hidden">
        @if ($imageSrc)
            <img
                src="{{ $imageSrc }}"
                alt=""
                class="h-40 w-full object-cover transition duration-500 ease-out group-hover:scale-[1.04] sm:h-36"
                loading="lazy"
            >
        @else
            <div class="h-40 w-full bg-gradient-to-br from-primary/15 to-secondary/10 sm:h-36" aria-hidden="true"></div>
        @endif
        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-secondary/70 to-transparent p-4">
            <x-content.content-meta :content="$content" :on-dark="true" />
        </div>
    </a>

    <div class="flex flex-1 flex-col p-5">
        <h3 class="line-clamp-3 text-lg font-semibold leading-snug text-secondary">
            <a href="{{ route('contents.show', $content['slug']) }}" class="transition hover:text-primary">
                {{ $content['title'] }}
            </a>
        </h3>
        <p class="mt-3 line-clamp-4 flex-1 text-sm leading-relaxed text-secondary-light">
            {{ $content['summary'] }}
        </p>

        <div class="mt-5 flex flex-wrap items-center gap-2">
            <x-ui.button href="{{ route('contents.show', $content['slug']) }}" variant="secondary" class="!rounded-2xl">
                Leer noticia
            </x-ui.button>
            @if ($isExternal && ! empty($content['external_url']))
                <x-ui.button
                    href="{{ $content['external_url'] }}"
                    variant="secondary"
                    class="!rounded-2xl"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Fuente oficial
                </x-ui.button>
            @endif
        </div>
    </div>
</article>
