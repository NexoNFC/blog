<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Administración') — FESC</title>
    <meta name="theme-color" content="#c8102e">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-body admin-shell min-h-screen" x-data="adminShell">
    <div class="ambient-bg" aria-hidden="true">
        <div class="ambient-blob ambient-blob--1"></div>
        <div class="ambient-blob ambient-blob--2"></div>
        <div class="ambient-blob ambient-blob--3"></div>
    </div>

    <div class="site-content relative min-h-screen">
        <x-navigation.admin-topbar />
        <x-navigation.admin-sidebar />

        <div
            class="fixed inset-0 z-30 bg-secondary/40 backdrop-blur-[2px] sm:hidden"
            x-show="open"
            x-transition.opacity
            x-cloak
            @click="closeMobile()"
            aria-hidden="true"
        ></div>

        <div
            class="admin-main min-h-screen p-4 pt-[4.75rem] transition-[margin] duration-300 sm:p-6 sm:pt-[5rem]"
            :class="open ? 'sm:ml-64' : 'sm:ml-0'"
        >
            <header class="admin-page-header mb-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <p class="admin-page-header__eyebrow">Administración FESC</p>
                        <h1 class="admin-page-header__title">@yield('heading', 'Administración')</h1>
                        @hasSection('subtitle')
                            <p class="admin-page-header__subtitle">@yield('subtitle')</p>
                        @endif
                    </div>
                    @hasSection('actions')
                        <div class="admin-page-header__actions shrink-0">@yield('actions')</div>
                    @endif
                </div>
            </header>

            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>

    <x-ui.alert-host />
    @stack('scripts')
</body>
</html>
