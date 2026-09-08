<section id="nfc" class="footer-glass py-16 text-white sm:py-20">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:gap-16">
        <div class="reveal order-2 lg:order-1">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-primary">Experiencia NFC</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-white sm:text-4xl">
                Acerca tu teléfono y descubre qué hay aquí
            </h2>
            <p class="mt-4 max-w-xl text-base leading-relaxed text-white/80">
                Los chips del campus no guardan una noticia fija. Identifican un punto. Desde la plataforma se decide qué información muestra ese lugar hoy.
            </p>

            <ul class="mt-8 space-y-4">
                <li class="flex gap-3">
                    <x-ui.icon name="arrow-right-to-bracket" class="!bg-white/10 !text-white" />
                    <div>
                        <p class="font-semibold">Lugar físico</p>
                        <p class="text-sm text-white/70">Biblioteca, entradas, bloques y auditorio.</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <x-ui.icon name="atom" class="!bg-white/10 !text-white" />
                    <div>
                        <p class="font-semibold">Punto NFC estable</p>
                        <p class="text-sm text-white/70">Una URL permanente, contenido administrable.</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <x-ui.icon name="book" class="!bg-white/10 !text-white" />
                    <div>
                        <p class="font-semibold">Descubrimiento</p>
                        <p class="text-sm text-white/70">Noticias, eventos y enlaces oficiales de FESC.</p>
                    </div>
                </li>
            </ul>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <x-ui.button href="{{ route('nfc.show', 'biblioteca') }}">Probar punto Biblioteca</x-ui.button>
                <x-ui.button href="{{ route('nfc.show', 'entrada-avenida-5') }}" variant="secondary" class="!border-white/30 !bg-transparent !text-white hover:!bg-white/10">
                    Probar Entrada Av. 5
                </x-ui.button>
            </div>
        </div>

        <div class="reveal reveal-delay-2 order-1 lg:order-2">
            <div class="relative mx-auto max-w-md">
                <img
                    src="{{ asset('images/campus/estudiantes-nfc.jpg') }}"
                    alt="Estudiante con el teléfono en el acceso del campus FESC"
                    class="w-full rounded-2xl border border-white/10 object-cover shadow-2xl"
                    loading="lazy"
                >
                <div class="pointer-events-none absolute inset-0 flex items-center justify-center" aria-hidden="true">
                    <span class="nfc-pulse absolute h-28 w-28 rounded-full border-2 border-primary/80"></span>
                    <span class="nfc-pulse nfc-pulse-delay absolute h-44 w-44 rounded-full border border-white/50"></span>
                    <span class="nfc-pulse nfc-pulse-delay-2 absolute h-60 w-60 rounded-full border border-white/25"></span>
                </div>
            </div>
            <x-landing.journey
                class="mt-4 justify-center"
                :on-dark="true"
                :steps="[
                    ['icon' => 'blender-phone', 'label' => 'Teléfono'],
                    ['icon' => 'atom', 'label' => 'Punto NFC'],
                    ['icon' => 'book', 'label' => 'Contenido'],
                ]"
            />
        </div>
    </div>
</section>
