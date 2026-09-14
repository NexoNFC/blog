<section id="nfc" class="footer-glass py-16 text-white sm:py-20">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:gap-16">
        <div class="order-2 lg:order-1" data-reveal-stagger data-reveal-step="75">
            <p class="reveal text-xs font-semibold uppercase tracking-[0.2em] text-primary">Experiencia NFC</p>
            <h2 class="reveal mt-3 text-3xl font-bold tracking-tight text-white sm:text-4xl">
                Acerca tu teléfono y descubre qué hay aquí
            </h2>
            <p class="reveal mt-4 max-w-xl text-base leading-relaxed text-white/80">
                Los chips del campus no guardan una noticia fija. Identifican un punto. Desde la plataforma se decide qué información muestra ese lugar hoy.
            </p>

            <ul class="mt-8 space-y-4">
                <li class="reveal flex gap-3">
                    <x-ui.icon name="home" class="!bg-white/10 !text-white" />
                    <div>
                        <p class="font-semibold">Lugar físico</p>
                        <p class="text-sm text-white/70">Biblioteca, entradas, bloques y auditorio.</p>
                    </div>
                </li>
                <li class="reveal flex gap-3">
                    <x-ui.icon name="degrees-360" class="!bg-white/10 !text-white" />
                    <div>
                        <p class="font-semibold">Punto NFC estable</p>
                        <p class="text-sm text-white/70">Una URL permanente, contenido administrable.</p>
                    </div>
                </li>
                <li class="reveal flex gap-3">
                    <x-ui.icon name="search" class="!bg-white/10 !text-white" />
                    <div>
                        <p class="font-semibold">Descubrimiento</p>
                        <p class="text-sm text-white/70">Noticias, eventos y enlaces oficiales de FESC.</p>
                    </div>
                </li>
            </ul>

            <div class="reveal mt-8 flex flex-col gap-3 sm:flex-row">
                <x-ui.button href="{{ route('nfc.show', 'biblioteca') }}">Probar punto Biblioteca</x-ui.button>
                <x-ui.button href="{{ route('nfc.show', 'entrada-avenida-5') }}" variant="secondary" class="!border-white/30 !bg-transparent !text-white hover:!bg-white/10">
                    Probar Entrada Av. 5
                </x-ui.button>
            </div>
        </div>

        <div class="reveal reveal-delay-2 order-1 lg:order-2">
            <div class="nfc-media relative mx-auto max-w-md">
                <img
                    src="{{ asset('images/campus/estudiantes-nfc.jpg') }}"
                    alt="Estudiante con el teléfono en el acceso del campus FESC"
                    class="w-full rounded-2xl border border-white/10 object-cover shadow-2xl"
                    loading="lazy"
                >
                <span class="nfc-media__scan" aria-hidden="true"></span>
                <x-nfc.signal class="!absolute inset-0 z-10 m-auto h-64 w-64 sm:h-72 sm:w-72" on-dark />
                <div class="nfc-media__telemetry" aria-hidden="true">
                    <span>13.56 MHz</span>
                    <span>ENLACE SEGURO</span>
                </div>
            </div>
            <x-landing.journey
                class="mt-4 justify-center"
                :on-dark="true"
                :steps="[
                    ['icon' => 'smartphone-rotate', 'label' => 'Teléfono'],
                    ['icon' => 'degrees-360', 'label' => 'Punto NFC'],
                    ['icon' => 'browsers', 'label' => 'Contenido'],
                ]"
            />
        </div>
    </div>
</section>
