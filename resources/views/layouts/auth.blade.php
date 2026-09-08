<!DOCTYPE html>
<html lang="es" class="h-dvh">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Acceso administrativo' }} — FESC</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-dvh overflow-hidden font-sans antialiased">
    <div class="scrollbar-hidden relative h-dvh overflow-x-hidden overflow-y-auto lg:overflow-hidden">
        <aside class="relative min-h-[22rem] overflow-hidden sm:min-h-[28rem] lg:h-dvh">
            <img
                src="{{ asset('images/campus/fachada.jpg') }}"
                alt=""
                class="absolute inset-0 h-full w-full object-cover"
            >
            <div class="absolute inset-0 bg-gradient-to-br from-red-950/85 via-red-950/70 to-neutral-900/90"></div>

            <div class="relative z-10 flex min-h-[22rem] flex-col justify-between px-6 py-8 sm:min-h-[28rem] sm:px-10 sm:py-10 lg:h-dvh lg:max-w-[58%] lg:px-16 lg:py-14">
                <div>
                    <a href="{{ route('home') }}" class="inline-flex flex-col text-white">
                        <span class="font-serif text-3xl font-bold tracking-tight sm:text-4xl">FESC</span>
                        <span class="mt-1 font-sans text-[0.7rem] font-medium tracking-[0.22em] text-white/70">EDUCACIÓN SUPERIOR</span>
                    </a>

                    <p class="mt-10 font-serif text-4xl font-bold tracking-tight text-white sm:text-5xl lg:mt-16 lg:text-6xl">
                        <span class="block">Acceso</span>
                        <span class="block">administrativo</span>
                    </p>
                    <p class="mt-4 max-w-md font-sans text-sm leading-relaxed text-white/70 sm:text-base">
                        Plataforma informativa FESC. Ingreso restringido para administradores.
                        La consulta pública no requiere cuenta.
                    </p>
                </div>

                <div class="mt-10 flex items-end justify-between gap-4 lg:mt-0">
                    <p class="max-w-[16rem] text-xs text-white/50">
                        Fundación de Estudios Superiores Comfanorte
                    </p>
                    <a href="{{ route('home') }}" class="shrink-0 text-xs text-white/60 underline-offset-2 hover:text-white hover:underline">
                        Volver al sitio
                    </a>
                </div>
            </div>
        </aside>

        <main class="scrollbar-hidden relative z-20 px-4 pb-10 text-text sm:px-6 lg:absolute lg:inset-y-0 lg:right-10 lg:flex lg:items-center lg:overflow-y-auto lg:px-0 xl:right-20">
            <div class="mx-auto -mt-16 w-full max-w-md lg:mx-0 lg:mt-0">
                {{ $slot }}
            </div>
        </main>
    </div>
    <x-ui.alert-host />
</body>
</html>
