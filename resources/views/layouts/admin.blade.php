<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Administración') — FESC</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background">
    <div class="flex min-h-screen flex-col lg:flex-row">
        <x-navigation.admin-sidebar />

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="border-b border-muted bg-surface px-4 py-4 sm:px-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-secondary sm:text-2xl">@yield('heading', 'Administración')</h1>
                        @hasSection('subtitle')
                            <p class="mt-0.5 text-sm text-secondary-light">@yield('subtitle')</p>
                        @endif
                    </div>
                    @hasSection('actions')
                        <div class="shrink-0">@yield('actions')</div>
                    @endif
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
