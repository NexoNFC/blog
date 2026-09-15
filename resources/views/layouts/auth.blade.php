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
<body class="site-body auth-body antialiased">
    <div class="auth-shell">
        <div class="auth-shell__media" aria-hidden="true">
            <img
                src="{{ asset('images/campus/fachada.jpg') }}"
                alt=""
            >
            <div class="auth-shell__scrim"></div>
            <div class="auth-shell__atmosphere">
                <span class="auth-shell__orb auth-shell__orb--one"></span>
                <span class="auth-shell__orb auth-shell__orb--two"></span>
                <span class="auth-shell__grid"></span>
            </div>
        </div>

        <div class="auth-shell__content">
            <aside class="auth-shell__brand">
                <div>
                    <a href="{{ route('home') }}" class="auth-shell__logo">
                        <span class="auth-shell__badge">FESC</span>
                        <span class="auth-shell__logo-text">
                            <span>Información</span>
                            <span>en campus</span>
                        </span>
                    </a>

                    <p class="auth-shell__eyebrow">Administración</p>
                    <p class="auth-shell__title">
                        <span class="block">Acceso</span>
                        <span class="auth-shell__title-accent">administrativo</span>
                    </p>
                    <p class="auth-shell__lead">
                        Plataforma informativa FESC. Ingreso restringido para administradores.
                        La consulta pública no requiere cuenta.
                    </p>
                </div>

                <div class="auth-shell__meta auth-shell__meta--desktop">
                    <p>Fundación de Estudios Superiores Comfanorte</p>
                    <a href="{{ route('home') }}" class="auth-shell__back">Volver al sitio</a>
                </div>
            </aside>

            <main class="auth-shell__panel">
                <div class="auth-shell__panel-inner">
                    {{ $slot }}
                </div>

                <div class="auth-shell__meta auth-shell__meta--mobile">
                    <p>Fundación de Estudios Superiores Comfanorte</p>
                    <a href="{{ route('home') }}" class="auth-shell__back">Volver al sitio</a>
                </div>
            </main>
        </div>
    </div>
    <x-ui.alert-host />
</body>
</html>
