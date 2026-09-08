@props([
    'items' => [],
    'moreNews' => [],
])

<section class="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-10">
    <div class="glass-card px-6 py-8 sm:px-10">
        <h2 class="text-xl font-semibold tracking-tight sm:text-2xl">Información de este punto</h2>
    <p class="mt-1 text-sm text-secondary-light">
        El contenido puede cambiar sin reprogramar el chip físico.
    </p>

    @forelse ($items as $content)
        <article class="mt-6">
            <x-content.content-meta :content="$content" />

            <h3 class="mt-3 text-2xl font-bold sm:text-3xl">{{ $content['title'] }}</h3>
            <p class="mt-3 text-base text-secondary-light sm:text-lg">{{ $content['summary'] }}</p>

            @if (! empty($content['image']))
                <div class="mt-6 overflow-hidden rounded-[20px] border border-white/50 bg-white/40">
                    <img
                        src="{{ asset($content['image']) }}"
                        alt="{{ $content['title'] }}"
                        class="aspect-[16/9] h-full w-full object-cover"
                        loading="lazy"
                    >
                </div>
            @endif

            <div class="mt-6 space-y-4 text-base leading-relaxed text-secondary">
                @foreach (preg_split("/\n\n/", (string) $content['body']) as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>

            @if (! empty($content['external_url']))
                <div class="mt-8 space-y-4">
                    <x-content.external-source :url="$content['external_url']" label="Consultar fuente original" />
                </div>
            @endif

            <div class="mt-8">
                <x-ui.button href="{{ route('contents.show', $content['slug']) }}" variant="secondary">
                    Ver noticia completa
                </x-ui.button>
            </div>
        </article>
    @empty
        <div class="mt-6">
            <x-ui.empty-state
                title="Sin noticia asociada"
                description="Este punto está activo, pero todavía no tiene una noticia publicada."
            />
        </div>
    @endforelse

    @if ($moreNews !== [])
        <nav class="mt-12 border-t border-white/40 pt-8" aria-label="Otras noticias">
            <h3 class="text-lg font-semibold">Otras noticias</h3>
            <ul class="mt-4 space-y-3">
                @foreach ($moreNews as $item)
                    <li>
                        <a href="{{ route('contents.show', $item['slug']) }}" class="font-semibold text-primary hover:underline">
                            {{ $item['title'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    @endif
    </div>
</section>
