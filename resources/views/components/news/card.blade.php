@props([
    'content',
    'image' => null,
    'featured' => false,
])

@php
    $isExternal = ($content['type'] ?? null) === 'externo' || ! empty($content['external_url']);
    $imageSrc = $image ?? ($content['image'] ?? asset('images/landing/news-campus-walk.png'));
@endphp

<article {{ $attributes->merge([
    'class' => $featured
        ? 'group reveal flex h-full flex-col overflow-hidden rounded-2xl border border-muted bg-surface shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl sm:col-span-2 lg:col-span-2 lg:row-span-2'
        : 'group reveal flex h-full flex-col overflow-hidden rounded-2xl border border-muted bg-surface shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl',
]) }}>
    <a href="{{ route('contents.show', $content['slug']) }}" class="relative block overflow-hidden">
        <img
            src="{{ $imageSrc }}"
            alt=""
            class="{{ $featured ? 'aspect-[16/10]' : 'aspect-[16/11]' }} w-full object-cover transition duration-500 group-hover:scale-[1.03]"
            loading="lazy"
        >
        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-secondary/70 to-transparent p-4">
            <x-content.content-meta :content="$content" :on-dark="true" />
        </div>
    </a>

    <div class="flex flex-1 flex-col p-5">
        <h3 @class(['font-serif font-semibold text-secondary', 'text-2xl sm:text-3xl' => $featured, 'text-xl' => ! $featured])>
            <a href="{{ route('contents.show', $content['slug']) }}" class="transition hover:text-primary">
                {{ $content['title'] }}
            </a>
        </h3>
        <p @class(['mt-3 flex-1 text-secondary-light', 'text-base' => $featured, 'text-sm' => ! $featured])>
            {{ $content['summary'] }}
        </p>

        <div class="mt-5 flex flex-wrap items-center gap-2">
            <x-ui.button href="{{ route('contents.show', $content['slug']) }}" variant="secondary">
                Leer noticia
            </x-ui.button>
            @if ($isExternal && ! empty($content['external_url']))
                <x-content.external-source :url="$content['external_url']" label="Fuente oficial" />
            @endif
        </div>
    </div>
</article>
