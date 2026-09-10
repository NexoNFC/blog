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
<body class="bg-secondary font-sans antialiased">
    <div class="auth-shell">
        <div class="auth-shell__media" aria-hidden="true">
            <img
                src="{{ asset('images/campus/fachada.jpg') }}"
                alt=""
            >
            <div class="auth-shell__scrim"></div>
        </div>

        <div class="auth-shell__content">
            <aside class="auth-shell__brand">
                <div>
                    <a href="{{ route('home') }}" class="inline-flex flex-col text-white">
                        <span class="font-serif text-2xl font-bold tracking-tight sm:text-3xl lg:text-4xl">FESC</span>
                        <span class="mt-1 font-sans text-[0.65rem] font-medium tracking-[0.22em] text-white/70 sm:text-[0.7rem]">EDUCACIÓN SUPERIOR</span>
                    </a>

                    <p class="auth-shell__title">
                        <span class="block">Acceso</span>
                        <span class="block">administrativo</span>
                    </p>
                    <p class="auth-shell__lead">
                        Plataforma informativa FESC. Ingreso restringido para administradores.
                        La consulta pública no requiere cuenta.
                    </p>
                </div>

                <div class="auth-shell__meta auth-shell__meta--desktop">
                    <p>Fundación de Estudios Superiores Comfanorte</p>
                    <a href="{{ route('home') }}">Volver al sitio</a>
                </div>
            </aside>

            <main class="auth-shell__panel">
                <div class="auth-shell__panel-inner">
                    {{ $slot }}
                </div>

                <div class="auth-shell__meta auth-shell__meta--mobile">
                    <p>Fundación de Estudios Superiores Comfanorte</p>
                    <a href="{{ route('home') }}">Volver al sitio</a>
                </div>
            </main>
        </div>
    </div>
    <x-ui.alert-host />
</body>
</html>
