<section class="relative -mt-20 overflow-hidden pt-20 text-white">
    <div class="absolute inset-0" aria-hidden="true">
        <img
            src="{{ asset('images/campus/aereo.jpg') }}"
            alt=""
            class="h-full w-full object-cover opacity-35"
            width="1920"
            height="1080"
            fetchpriority="high"
        >
        <div class="absolute inset-0 bg-gradient-to-br from-text/90 via-primary-dark/70 to-text/85"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-text/55 via-text/20 to-transparent"></div>
    </div>

    <div class="relative mx-auto grid max-w-screen-xl items-center gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:gap-16 lg:py-24">
        <div class="reveal">
            <p class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold tracking-[0.18em] text-white/85 uppercase backdrop-blur-md">
                Comunidad FESC · Campus + NFC
            </p>

            <h1 class="mt-6 max-w-xl font-serif text-4xl font-bold leading-[1.12] text-white sm:text-5xl lg:text-6xl">
                Descubre FESC
                <span class="block">donde estás</span>
            </h1>

            <p class="mt-5 max-w-lg font-sans text-base leading-relaxed text-white/75 sm:text-lg">
                Noticias, eventos y mensajes de la comunidad. En el campus, acerca tu teléfono a un punto NFC y abre la información de ese lugar.
            </p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                <x-ui.button href="#noticias" class="w-full !rounded-lg !font-bold sm:w-auto">
                    Explorar noticias
                </x-ui.button>
                <x-ui.button href="#como-funciona" variant="glass" class="w-full !rounded-lg sm:w-auto">
                    Descubrir cómo funciona
                </x-ui.button>
            </div>

            <dl class="mt-10 grid max-w-lg grid-cols-3 gap-4 border-t border-white/10 pt-6">
                <div>
                    <dt class="text-xs font-medium tracking-wide text-white/55 uppercase">Bloques</dt>
                    <dd class="mt-1 font-serif text-2xl font-bold text-white">A · B · C</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium tracking-wide text-white/55 uppercase">Accesos</dt>
                    <dd class="mt-1 font-serif text-2xl font-bold text-white">Av. 4/5</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium tracking-wide text-white/55 uppercase">Canal</dt>
                    <dd class="mt-1 font-serif text-2xl font-bold text-white">NFC</dd>
                </div>
            </dl>
        </div>

        <div class="reveal reveal-delay-2">
            <div class="rounded-2xl border border-white/20 bg-white/10 p-3 shadow-2xl backdrop-blur-lg">
                <img
                    src="{{ asset('images/campus/estudiantes-fachada.jpg') }}"
                    alt="Estudiantes frente a la sede FESC en Cúcuta"
                    class="aspect-[4/3] w-full rounded-xl object-cover"
                    width="1536"
                    height="1024"
                    loading="eager"
                >
            </div>
            <x-landing.journey
                class="mt-4"
                :steps="[
                    ['icon' => 'bookmark', 'label' => 'Campus'],
                    ['icon' => 'atom', 'label' => 'NFC'],
                    ['icon' => 'blender-phone', 'label' => 'Tu teléfono'],
                    ['icon' => 'book', 'label' => 'Información'],
                ]"
            />
        </div>
    </div>
</section>
