@props([
    'steps' => [],
])

<section id="como-funciona" class="py-16 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <x-ui.section-heading
            class="reveal"
            eyebrow="Cómo funciona"
            title="Cuatro pasos. Cero fricción."
            description="Encuentra un punto en el campus, acerca el teléfono y explora lo que hay disponible en ese lugar."
        />

        <ol class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($steps as $index => $step)
                <li class="reveal glass-card glass-hover p-6" style="--reveal-delay: {{ ($index + 1) * 80 }}ms">
                    <div class="flex items-center justify-between gap-3">
                        <x-ui.icon :name="$step['icon']" />
                        <span class="text-2xl font-bold tabular-nums text-primary/30">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h3 class="mt-5 text-xl font-semibold text-secondary">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-secondary-light">{{ $step['description'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
