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
            <div class="nfc-stage" aria-hidden="true">
                <span class="nfc-stage__glow"></span>
                <span class="nfc-stage__grid"></span>

                <div class="nfc-stage__field">
                    <span class="nfc-stage__ring nfc-stage__ring--outer"></span>
                    <span class="nfc-stage__ring nfc-stage__ring--middle"></span>
                    <span class="nfc-stage__ring nfc-stage__ring--inner"></span>
                    <span class="nfc-stage__pulse"></span>
                    <span class="nfc-stage__node nfc-stage__node--a"></span>
                    <span class="nfc-stage__node nfc-stage__node--b"></span>
                    <span class="nfc-stage__node nfc-stage__node--c"></span>
                </div>

                <div class="nfc-stage__chip nfc-stage__chip--coin">
                    <span class="fesc-coin nfc-stage__coin" aria-hidden="true">
                        <span class="fesc-coin__layer fesc-coin__layer--back"></span>
                        <span class="fesc-coin__layer fesc-coin__layer--back-middle"></span>
                        <span class="fesc-coin__layer fesc-coin__layer--middle"></span>
                        <span class="fesc-coin__layer fesc-coin__layer--front-middle"></span>
                        <span class="fesc-coin__layer fesc-coin__layer--front"></span>
                        <span class="fesc-coin__face fesc-coin__face--front">
                            <strong>FESC</strong>
                            <small>CÚCUTA</small>
                        </span>
                        <span class="fesc-coin__face fesc-coin__face--back">
                            <strong>NFC</strong>
                            <small>CÚCUTA</small>
                        </span>
                        <span class="fesc-coin__rim"></span>
                    </span>
                </div>

                <div class="nfc-stage__phone">
                    <span class="nfc-stage__phone-notch"></span>
                    <span class="nfc-stage__phone-screen">
                        <span class="nfc-stage__phone-wave"></span>
                        <span class="nfc-stage__phone-wave"></span>
                        <span class="nfc-stage__phone-wave"></span>
                    </span>
                </div>

                <div class="nfc-stage__labels">
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
