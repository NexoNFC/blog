@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#c8102e">
    <title>{{ $title ?? 'Información FESC' }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-body flex min-h-screen flex-col antialiased">
    <div class="ambient-bg" aria-hidden="true">
        <div class="ambient-blob ambient-blob--1"></div>
        <div class="ambient-blob ambient-blob--2"></div>
        <div class="ambient-blob ambient-blob--3"></div>
    </div>

    <div class="site-content flex min-h-screen flex-col">
        <header class="border-b border-primary/10 bg-white/70 backdrop-blur-md">
            <div class="mx-auto flex h-14 max-w-5xl items-center justify-center px-6">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                    <span class="inline-flex h-8 items-center justify-center rounded-lg bg-primary px-2 text-[0.65rem] font-bold tracking-[0.14em] text-white">
                        FESC
                    </span>
                    <span class="text-sm font-semibold tracking-tight text-secondary">Información en campus</span>
                </a>
            </div>
        </header>

        <main class="flex flex-1 items-center">
            <div class="mx-auto w-full max-w-5xl px-6 py-10 md:py-14">
                {{ $slot }}
            </div>
        </main>

        <footer class="border-t border-primary/10 bg-white/50 py-4 text-center text-xs text-secondary-light backdrop-blur-md">
            Fundación de Estudios Superiores Comfanorte · Plataforma informativa
        </footer>
    </div>
</body>
</html>
