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
    </div>

    <div class="site-content flex min-h-screen flex-col">
        <x-navigation.navbar />

        <main class="flex-1 pt-[5.75rem]">
            @yield('content')
        </main>

        <x-navigation.site-footer />
        <x-ui.alert-host />
    </div>
</body>
</html>
