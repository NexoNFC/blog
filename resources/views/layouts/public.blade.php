<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Información FESC')</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    <x-navigation.navbar />

    <main class="flex-1">
        @yield('content')
    </main>

    <x-navigation.site-footer />
</body>
</html>
