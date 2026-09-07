@props([
    'featured' => null,
    'items' => [],
    'images' => [],
])

<section id="noticias" class="py-16 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <x-ui.section-heading
                class="reveal"
                eyebrow="Explora"
                title="Lo que está pasando en la comunidad"
                description="Noticias, eventos y comunicados. También puedes llegar a ellos desde un punto NFC del campus."
            />
            <x-ui.button href="#campus" variant="ghost" class="reveal shrink-0 self-start sm:self-auto">
                Ver puntos del campus
            </x-ui.button>
        </div>

        @if ($featured || count($items))
            <div class="mt-12 grid items-start gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @if ($featured)
                    <x-news.card
                        :content="$featured"
                        :featured="true"
                        :image="$images[$featured['slug']] ?? null"
                    />
                @endif

                @foreach ($items as $item)
                    <x-news.card
                        :content="$item"
                        :image="$images[$item['slug']] ?? null"
                    />
                @endforeach
            </div>
        @else
            <div class="mt-10">
                <x-ui.empty-state title="Aún no hay noticias publicadas" description="Pronto verás aquí lo más reciente de la comunidad FESC." />
            </div>
        @endif
    </div>
</section>
