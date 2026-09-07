@props([
    'contents' => [],
])

<section {{ $attributes->merge(['class' => 'mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10']) }}>
    <h2 class="text-xl font-semibold sm:text-2xl">Información disponible en este punto</h2>
    <p class="mt-1 text-sm text-secondary-light">
        El contenido puede cambiar sin reprogramar el chip físico.
    </p>

    <div class="mt-6 grid gap-4 sm:gap-6">
        @forelse ($contents as $content)
            <x-content.content-card :content="$content" />
        @empty
            <x-ui.empty-state
                title="Sin contenidos asociados"
                description="Este punto está activo, pero todavía no tiene información publicada."
            />
        @endforelse
    </div>
</section>
