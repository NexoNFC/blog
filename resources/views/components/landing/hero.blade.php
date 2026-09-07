<section class="relative overflow-hidden bg-secondary text-white">
    <div class="absolute inset-0">
        <img
            src="{{ asset('images/landing/hero-nfc-campus.png') }}"
            alt="Estudiante acercando el teléfono a un punto NFC en un pasillo universitario"
            class="h-full w-full object-cover opacity-45"
            width="1600"
            height="900"
            fetchpriority="high"
        >
        <div class="absolute inset-0 bg-gradient-to-r from-secondary via-secondary/85 to-secondary/40"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,rgba(200,16,46,0.35),transparent_45%)]"></div>
    </div>

    <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-12 lg:items-center lg:py-24">
        <div class="reveal lg:col-span-7">
            <p class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-white/85 backdrop-blur">
                Comunidad FESC · Campus + NFC
            </p>

            <h1 class="mt-5 max-w-3xl font-serif text-4xl font-bold leading-[1.1] text-white sm:text-5xl lg:text-6xl">
                Descubre FESC donde estás
            </h1>

            <p class="mt-5 max-w-xl text-base leading-relaxed text-white/85 sm:text-lg">
                Noticias, eventos y mensajes de la comunidad. En el campus, acerca tu teléfono a un punto NFC y abre la información de ese lugar.
            </p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <x-ui.button href="#noticias" class="w-full sm:w-auto">Explorar noticias</x-ui.button>
                <x-ui.button href="#como-funciona" variant="secondary" class="w-full !border-white/35 !bg-white/5 !text-white hover:!bg-white/15 sm:w-auto">
                    Descubrir cómo funciona
                </x-ui.button>
            </div>

            <dl class="mt-10 grid max-w-lg grid-cols-3 gap-4 border-t border-white/15 pt-6 text-center sm:text-left">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-white/60">Bloques</dt>
                    <dd class="mt-1 font-serif text-2xl font-bold">A · B · C</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-white/60">Accesos</dt>
                    <dd class="mt-1 font-serif text-2xl font-bold">Av. 4/5</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-white/60">Canal</dt>
                    <dd class="mt-1 font-serif text-2xl font-bold">NFC</dd>
                </div>
            </dl>
        </div>

        <div class="reveal reveal-delay-2 relative lg:col-span-5">
            <div class="relative mx-auto max-w-sm overflow-hidden rounded-2xl border border-white/15 bg-white/10 p-3 shadow-2xl backdrop-blur-sm">
                <img
                    src="{{ asset('images/landing/nfc-phone-tap.png') }}"
                    alt="Teléfono cerca de un punto NFC"
                    class="aspect-square w-full rounded-xl object-cover"
                    width="800"
                    height="800"
                    loading="lazy"
                >
                <div class="pointer-events-none absolute inset-0 flex items-center justify-center" aria-hidden="true">
                    <span class="nfc-pulse absolute h-24 w-24 rounded-full border border-primary/70"></span>
                    <span class="nfc-pulse nfc-pulse-delay absolute h-36 w-36 rounded-full border border-white/40"></span>
                </div>
            </div>
            <p class="mt-4 text-center text-sm text-white/70">Campus físico → NFC → tu teléfono → información</p>
        </div>
    </div>
</section>
