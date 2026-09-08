@php
    $partners = [
        ['src' => 'images/brand/comfanorte.png', 'alt' => 'Comfanorte', 'href' => 'https://www.comfanorte.com.co/'],
        ['src' => 'images/brand/sies-plus.png', 'alt' => 'SIES+', 'href' => 'https://siesmas.co/'],
        ['src' => 'images/brand/icetex.png', 'alt' => 'ICETEX', 'href' => 'https://www.icetex.gov.co/'],
        ['src' => 'images/brand/icfes.png', 'alt' => 'ICFES', 'href' => 'https://www.icfes.gov.co/'],
        ['src' => 'images/brand/sena.png', 'alt' => 'SENA', 'href' => 'https://www.sena.edu.co/'],
        ['src' => 'images/brand/educajas.png', 'alt' => 'Educajas', 'href' => null],
        ['src' => 'images/brand/men.png', 'alt' => 'Ministerio de Educación Nacional', 'href' => 'https://www.mineducacion.gov.co/'],
    ];
@endphp

<footer class="mt-auto">
    <div class="border-t border-white/40 bg-white/35 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-center gap-6 px-4 py-4 sm:px-6">
            @foreach ($partners as $partner)
                @if ($partner['href'])
                    <a href="{{ $partner['href'] }}" target="_blank" rel="noopener noreferrer" class="opacity-90 transition hover:opacity-100">
                        <img src="{{ asset($partner['src']) }}" alt="{{ $partner['alt'] }}" class="h-10 w-auto object-contain" loading="lazy">
                    </a>
                @else
                    <img src="{{ asset($partner['src']) }}" alt="{{ $partner['alt'] }}" class="h-10 w-auto object-contain opacity-90" loading="lazy">
                @endif
            @endforeach
        </div>
    </div>

    <div class="footer-glass text-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-10 sm:px-6 lg:grid-cols-12 lg:items-start lg:gap-8 lg:py-12">
            <div class="flex justify-center lg:col-span-3">
                <img
                    src="{{ asset('images/brand/icontec-fesc.png') }}"
                    alt="FESC Educación Superior, Comfanorte, certificación ICONTEC ISO 9001 e IQNET"
                    class="h-auto w-[179px] max-w-full"
                    width="179"
                    height="250"
                    loading="lazy"
                >
            </div>

            <div class="space-y-8 text-center text-sm lg:col-span-3">
                <div>
                    <h2 class="text-base font-bold">Sede Cúcuta:</h2>
                    <p class="mt-2 leading-relaxed text-white">
                        Av 5 # 15-27, Centro<br>
                        PBX +57 607 5880091 ext 101 102 103<br>
                        <a href="tel:+573123541578" class="hover:underline">312 354 1578</a>
                        /
                        <a href="tel:+573133860356" class="hover:underline">313 386 0356</a>
                    </p>
                </div>

                <div>
                    <h2 class="text-base font-bold">Sede Ocaña:</h2>
                    <p class="mt-2 leading-relaxed text-white">
                        Kdx 194-785, Vía Universitaria<br>
                        PBX +57 607 5880091 ext 203<br>
                        <a href="tel:+573133861614" class="hover:underline">313 386 1614</a>
                    </p>
                </div>
            </div>

            <div class="space-y-4 text-center lg:col-span-6 lg:text-left">
                <div class="text-center">
                    <h2 class="text-xl font-bold sm:text-2xl">Fundación de Estudios Superiores Comfanorte</h2>
                    <p class="mt-2 text-sm">
                        <a
                            href="https://www.fesc.edu.co/portal/archivos/reglamentos/personeria_juridica.pdf"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-white underline-offset-2 hover:underline"
                        >
                            Personería Jurídica: Resolución 04172 del 25 de agosto de 1993
                        </a>
                    </p>
                </div>

                <p class="text-sm leading-relaxed text-white lg:text-justify">
                    Institución de Educación Superior de carácter Tecnológico de derecho privado, de utilidad común y sin ánimo de lucro, redefinida mediante Resolución del MEN 747 del 19 de febrero de 2009, para ofertar programas Técnicos, Tecnológicos, Profesionales y Especializaciones.
                </p>

                <p class="text-sm leading-relaxed text-white lg:text-justify">
                    Su oferta académica se desarrolla en el Departamento Norte de Santander, específicamente en los municipios de San José de Cúcuta y en la Provincia de Ocaña.
                </p>
            </div>
        </div>
    </div>
</footer>

<a
    href="https://wa.me/573123541578"
    target="_blank"
    rel="noopener noreferrer"
    class="fixed bottom-5 right-5 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg transition hover:scale-105 hover:bg-[#1ebe57] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#25D366]"
    aria-label="Contactar por WhatsApp"
>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-7 w-7" aria-hidden="true">
        <path d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.9 11.9 0 0 0 5.76 1.47h.01c6.56 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.45-8.44ZM12.07 21.15h-.01a9.9 9.9 0 0 1-5.04-1.38l-.36-.21-3.74.98 1-3.64-.24-.37a9.86 9.86 0 0 1-1.51-5.26c0-5.45 4.44-9.88 9.9-9.88 2.64 0 5.12 1.03 6.99 2.9a9.82 9.82 0 0 1 2.9 6.98c0 5.45-4.44 9.88-9.89 9.88Zm5.72-7.4c-.31-.16-1.85-.91-2.14-1.02-.29-.1-.5-.16-.71.16-.21.31-.82 1.02-1.01 1.23-.18.21-.37.23-.68.08-.31-.16-1.32-.49-2.51-1.55-.93-.83-1.55-1.85-1.73-2.16-.18-.31-.02-.48.14-.63.14-.14.31-.37.47-.55.16-.18.21-.31.31-.52.1-.21.05-.39-.03-.55-.08-.16-.71-1.71-.97-2.34-.26-.63-.52-.54-.71-.55h-.6c-.21 0-.55.08-.84.39-.29.31-1.1 1.08-1.1 2.63s1.13 3.05 1.29 3.26c.16.21 2.22 3.39 5.38 4.75.75.32 1.34.52 1.8.66.76.24 1.45.21 2 .13.61-.09 1.85-.76 2.11-1.49.26-.73.26-1.36.18-1.49-.08-.13-.29-.21-.6-.37Z"/>
    </svg>
</a>
