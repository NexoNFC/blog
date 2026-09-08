@props([
    'content',
    'image' => null,
    'featured' => false,
])

@php
    $isExternal = ($content['type'] ?? null) === 'externo' || ! empty($content['external_url']);
    $imageSrc = $image ?? asset($content['image'] ?? 'images/campus/fachada.jpg');
@endphp

<article {{ $attributes->merge([
    'class' => $featured
        ? 'group glass-card glass-hover reveal flex w-full flex-col overflow-hidden sm:col-span-2 lg:col-span-2'
        : 'group glass-card glass-hover reveal flex w-full flex-col overflow-hidden',
]) }}>
    <a href="{{ route('contents.show', $content['slug']) }}" class="relative block overflow-hidden">
        <img
            src="{{ $imageSrc }}"
            alt=""
            class="{{ $featured ? 'h-52' : 'h-40' }} w-full object-cover transition duration-500 group-hover:scale-[1.03]"
            loading="lazy"
        >
        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-secondary/70 to-transparent p-4">
            <x-content.content-meta :content="$content" :on-dark="true" />
        </div>
    </a>

    <div class="flex flex-col p-6">
        <h3 @class(['font-semibold text-secondary', 'text-2xl sm:text-3xl' => $featured, 'text-xl' => ! $featured])>
            <a href="{{ route('contents.show', $content['slug']) }}" class="transition hover:text-primary">
                {{ $content['title'] }}
            </a>
        </h3>
        <p @class(['mt-3 text-secondary-light', 'text-base' => $featured, 'text-sm' => ! $featured])>
            {{ $content['summary'] }}
        </p>

        <div class="mt-5 flex flex-wrap items-center gap-2">
            <x-ui.button href="{{ route('contents.show', $content['slug']) }}" variant="secondary" class="!rounded-2xl">
                Leer noticia
            </x-ui.button>
            @if ($isExternal && ! empty($content['external_url']))
                <x-content.external-source :url="$content['external_url']" label="Fuente oficial" />
            @endif
        </div>
    </div>
</article>
