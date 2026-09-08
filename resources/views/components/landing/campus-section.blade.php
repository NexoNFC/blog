@props([
    'locations' => [],
])

<section id="campus" class="relative overflow-hidden py-16 sm:py-20">
    <div class="pointer-events-none absolute inset-y-0 right-0 hidden w-1/3 opacity-30 lg:block" aria-hidden="true">
        <img src="{{ asset('images/campus/fachada-desenfoque.jpg') }}" alt="" class="h-full w-full object-cover">
    </div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6">
        <x-ui.section-heading
            class="reveal"
            eyebrow="Campus"
            title="Información anclada a lugares reales"
            description="La plataforma se conecta con espacios de la sede Cúcuta. Cada punto NFC identifica un lugar; el contenido puede cambiar sin reprogramar el chip."
        />

        <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($locations as $location)
                <x-nfc.point-card :location="$location" />
            @endforeach
        </div>
    </div>
</section>
