@props([
    'items' => [],
    'moreNews' => [],
])

<div {{ $attributes->merge(['class' => 'space-y-8']) }}>
    @forelse ($items as $content)
        <article class="reveal glass-card overflow-hidden">
            @if (! empty($content['image']))
                <div class="relative aspect-[21/9] min-h-[14rem] overflow-hidden bg-secondary/10 sm:min-h-[18rem]">
                    <img
                        src="{{ asset($content['image']) }}"
                        alt="{{ $content['title'] }}"
                        class="h-full w-full object-cover"
                        loading="eager"
                    >
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-secondary/55 via-transparent to-transparent"></div>
                </div>
            @endif

            <div class="px-5 py-7 sm:px-8 sm:py-9 lg:px-10">
                @if (! empty($content['published_at']))
                    <time class="text-sm text-secondary-light" datetime="{{ $content['published_at'] }}">
                        {{ $content['published_at'] }}
                    </time>
                @endif

                <h1 @class([
                    'text-3xl font-bold tracking-tight text-secondary sm:text-4xl lg:text-5xl',
                    'mt-3' => ! empty($content['published_at']),
                ])>
                    {{ $content['title'] }}
                </h1>

                @if (! empty($content['summary']))
                    <p class="mt-4 max-w-4xl text-base leading-relaxed text-secondary-light sm:text-lg">
                        {{ $content['summary'] }}
                    </p>
                @endif

                <div class="mt-8 max-w-4xl space-y-4 text-base leading-relaxed text-secondary sm:text-[1.05rem]">
                    @foreach (preg_split("/\n\n/", (string) $content['body']) as $paragraph)
                        @if (trim((string) $paragraph) !== '')
                            <p>{{ $paragraph }}</p>
                        @endif
                    @endforeach
                </div>

                @if (! empty($content['external_url']))
                    <div class="mt-10 border-t border-white/40 pt-6">
                        <x-content.external-source :url="$content['external_url']" label="Ver en el sitio oficial de FESC" />
                    </div>
                @endif
            </div>
        </article>
    @empty
        <div class="reveal glass-card px-6 py-10 sm:px-10">
            <x-ui.empty-state
                title="Todavía no hay información aquí"
                description="Pronto encontrarás noticias y novedades asociadas a este punto del campus."
            />
        </div>
    @endforelse

    @if ($moreNews !== [])
        <nav class="reveal glass-card px-5 py-6 sm:px-8" aria-label="Otras noticias">
            <h2 class="text-lg font-semibold text-secondary">Otras noticias</h2>
            <ul class="mt-4 divide-y divide-white/30">
                @foreach ($moreNews as $item)
                    <li class="py-3 first:pt-0 last:pb-0">
                        <a href="{{ route('contents.show', $item['slug']) }}" class="font-semibold text-secondary transition hover:text-primary">
                            {{ $item['title'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    @endif
</div>
