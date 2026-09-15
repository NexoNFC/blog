<section class="hero-section px-4 pb-12 pt-4 sm:px-6 sm:pb-16 sm:pt-6">
    <div class="mx-auto grid max-w-7xl items-center gap-10 lg:grid-cols-2 lg:gap-14">
        <div class="text-center lg:text-left" data-reveal-stagger data-reveal-step="80">
            <h1 class="reveal text-4xl font-bold leading-[1.08] tracking-tight text-secondary sm:text-5xl lg:text-6xl">
                Descubre FESC
                <span class="gradient-text block">donde estás</span>
            </h1>

            <p class="reveal mx-auto mt-5 max-w-xl text-base leading-relaxed text-secondary lg:mx-0 sm:text-lg">
                Noticias, eventos y mensajes de la comunidad. En el campus, acerca tu teléfono a un punto NFC y abre la información de ese lugar.
            </p>

            <div class="reveal mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center lg:justify-start">
                <x-ui.button href="#noticias" class="w-full !rounded-2xl !px-6 !shadow-[0_12px_28px_rgb(200_16_46_/_0.22)] sm:w-auto">
                    Explorar noticias
                </x-ui.button>
                <x-ui.button href="#como-funciona" variant="secondary" class="w-full !rounded-2xl !border-primary/15 !bg-white/80 !text-secondary sm:w-auto">
                    Descubrir cómo funciona
                </x-ui.button>
            </div>
        </div>

        <div class="reveal reveal-delay-3 relative">
            <span class="hero-glow absolute -inset-5 rounded-[2.25rem] bg-primary/20 blur-2xl" aria-hidden="true"></span>
            <figure class="hero-photo relative">
                <img
                    src="{{ asset('images/campus/estudiantes-fachada.jpg') }}"
                    alt="Estudiantes frente a la sede FESC en Cúcuta"
                    class="hero-photo__image"
                    width="960"
                    height="720"
                    fetchpriority="high"
                >
                <figcaption class="absolute inset-x-4 bottom-4 z-10">
                    <span class="glass-chip px-3 py-1.5 text-xs font-semibold text-secondary">
                        <span class="nfc-status-dot" aria-hidden="true"></span>
                        Sede Cúcuta · red NFC activa
                    </span>
                </figcaption>
            </figure>
        </div>
    </div>
</section>
