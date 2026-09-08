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
<body class="site-body admin-shell min-h-screen">
    <div class="ambient-bg" aria-hidden="true">
        <div class="ambient-blob ambient-blob--1"></div>
        <div class="ambient-blob ambient-blob--2"></div>
        <div class="ambient-blob ambient-blob--3"></div>
    </div>

    <div class="site-content flex min-h-screen flex-col gap-3 p-3 lg:flex-row lg:p-4">
        <x-navigation.admin-sidebar />

        <div class="flex min-w-0 flex-1 flex-col gap-3">
            <header class="nav-glass sticky top-3 z-20 px-4 py-4 sm:px-6 lg:top-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-secondary sm:text-2xl">@yield('heading', 'Administración')</h1>
                        @hasSection('subtitle')
                            <p class="mt-0.5 text-sm text-secondary">@yield('subtitle')</p>
                        @endif
                    </div>
                    @hasSection('actions')
                        <div class="shrink-0">@yield('actions')</div>
                    @endif
                </div>
            </header>

            <main class="flex-1 px-1 py-2 sm:px-2">
                @yield('content')
            </main>
        </div>
    </div>
    <x-ui.alert-host />
</body>
</html>
