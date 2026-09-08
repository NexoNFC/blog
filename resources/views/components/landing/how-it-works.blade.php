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
                <li class="reveal group glass-panel rounded-2xl p-5 transition duration-300 hover:-translate-y-1 hover:border-primary/30 hover:shadow-[0_18px_50px_rgb(26_26_27_/_0.12)]" style="--reveal-delay: {{ ($index + 1) * 80 }}ms">
                    <div class="flex items-start justify-between gap-3">
                        <x-ui.icon :name="$step['icon']" />
                        <span class="font-serif text-3xl font-bold text-muted group-hover:text-primary/30">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h3 class="mt-5 text-lg font-semibold text-secondary">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-secondary-light">{{ $step['description'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
