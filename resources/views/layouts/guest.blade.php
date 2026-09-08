<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Acceso administrativo' }} — FESC</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans text-white antialiased">
    <div class="app-canvas-dark relative flex min-h-screen flex-col lg:flex-row">
        <aside class="relative flex flex-1 flex-col justify-between overflow-hidden px-6 py-10 sm:px-10 lg:max-w-xl lg:px-14 lg:py-14">
            <div class="relative">
                <x-brand.logo href="{{ route('home') }}" variant="light" />
                <p class="mt-8 font-serif text-3xl font-bold tracking-tight sm:text-4xl">
                    Administración
                </p>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-white/75 sm:text-base">
                    Acceso restringido para administradores de la plataforma informativa FESC.
                </p>
            </div>

            <p class="relative mt-10 text-xs text-white/45 lg:mt-0">
                Contenido público disponible sin inicio de sesión.
                <a href="{{ route('home') }}" class="ml-1 underline-offset-2 hover:text-white hover:underline">Volver al sitio</a>
            </p>
        </aside>

        <main class="relative flex flex-1 items-center justify-center px-4 py-10 text-text sm:px-8">
            <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(180deg,rgb(255_255_255_/0.08),transparent_40%,rgb(238_240_243_/0.55))] lg:rounded-s-[2.5rem]"></div>
            <div class="relative w-full max-w-md">
                {{ $slot }}
            </div>
        </main>
    </div>
    <x-ui.alert-host />
</body>
</html>
