<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Información FESC')</title>
    <meta name="theme-color" content="#c8102e">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-body flex min-h-screen flex-col">
    <div class="ambient-bg" aria-hidden="true">
        <div class="ambient-blob ambient-blob--1"></div>
        <div class="ambient-blob ambient-blob--2"></div>
        <div class="ambient-blob ambient-blob--3"></div>
        <div class="tech-backdrop">
            <span class="tech-backdrop__grid"></span>
            <span class="tech-backdrop__circuit tech-backdrop__circuit--left"></span>
            <span class="tech-backdrop__circuit tech-backdrop__circuit--right"></span>
            <span class="tech-backdrop__halo tech-backdrop__halo--one"></span>
            <span class="tech-backdrop__halo tech-backdrop__halo--two"></span>
            <span class="tech-backdrop__packet tech-backdrop__packet--one"></span>
            <span class="tech-backdrop__packet tech-backdrop__packet--two"></span>
            <span class="tech-backdrop__packet tech-backdrop__packet--three"></span>
            <span class="tech-backdrop__depth tech-backdrop__depth--left"></span>
            <span class="tech-backdrop__depth tech-backdrop__depth--right"></span>
        </div>
    </div>

    <div class="site-content flex min-h-screen flex-col">
        <x-navigation.navbar />

        <main class="flex-1 pt-[5.75rem]">
            @yield('content')
        </main>

        <x-navigation.site-footer />
        <x-ui.alert-host />
    </div>

    @if (request()->routeIs('home'))
        <button
            type="button"
            class="fesc-coin-scene fesc-coin-scene--tracker"
            data-fesc-coin
            data-nfc-tracker
            aria-label="Hacer girar la moneda FESC"
        >
            <span class="fesc-coin-scene__gimbal" data-nfc-gimbal>
                <x-nfc.signal class="fesc-coin-scene__signal" compact aria-hidden="true">
                    <span class="fesc-coin" aria-hidden="true">
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
                </x-nfc.signal>
            </span>
        </button>
    @endif
</body>
</html>
