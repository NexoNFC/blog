@php
    $navBase = 'nav-link block rounded-xl px-3 py-2.5 text-sm font-semibold whitespace-nowrap transition';
    $navOn = $navBase.' active bg-primary-soft';
    $navOff = $navBase.' hover:bg-primary-soft';
@endphp

<aside class="admin-sidebar lg:sticky lg:top-4 lg:flex lg:h-[calc(100vh-2rem)] lg:w-64 lg:shrink-0 lg:flex-col">
    <div class="flex items-center justify-between gap-3 px-4 py-4">
        <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-3">
            <span class="inline-flex h-9 shrink-0 items-center justify-center rounded-xl bg-primary px-2.5 text-[0.7rem] font-bold tracking-[0.14em] text-white shadow-[0_8px_16px_rgb(200_16_46_/_0.28)]">
                FESC
            </span>
            <span class="min-w-0">
                <span class="block text-sm font-bold leading-none tracking-tight text-secondary">Panel</span>
                <span class="mt-1 block text-xs font-medium leading-none text-secondary">administrativo</span>
            </span>
        </a>
        <a href="{{ route('home') }}" class="text-xs font-semibold text-primary underline-offset-2 hover:underline lg:hidden">Ver sitio</a>
    </div>

    <nav class="flex gap-1 overflow-x-auto px-3 pb-4 text-sm lg:flex-1 lg:flex-col lg:overflow-visible" aria-label="Administración">
        <a href="{{ route('admin.dashboard') }}" @class([
            $navOn => request()->routeIs('admin.dashboard'),
            $navOff => ! request()->routeIs('admin.dashboard'),
        ])>Dashboard</a>

        @can('news.view')
            <a href="{{ route('admin.news.index') }}" @class([
                $navOn => request()->routeIs('admin.news.*'),
                $navOff => ! request()->routeIs('admin.news.*'),
            ])>Noticias</a>
        @endcan

        @can('categories.view')
            <a href="{{ route('admin.categories.index') }}" @class([
                $navOn => request()->routeIs('admin.categories.*'),
                $navOff => ! request()->routeIs('admin.categories.*'),
            ])>Categorías</a>
        @endcan

        @can('nfc.view')
            <a href="{{ route('admin.nfc.index') }}" @class([
                $navOn => request()->routeIs('admin.nfc.*'),
                $navOff => ! request()->routeIs('admin.nfc.*'),
            ])>Puntos NFC</a>
        @endcan

        @can('statistics.view')
            <a href="{{ route('admin.statistics.index') }}" @class([
                $navOn => request()->routeIs('admin.statistics.*'),
                $navOff => ! request()->routeIs('admin.statistics.*'),
            ])>Estadísticas</a>
        @elsecan('statistics.view-content')
            <a href="{{ route('admin.statistics.index') }}" @class([
                $navOn => request()->routeIs('admin.statistics.*'),
                $navOff => ! request()->routeIs('admin.statistics.*'),
            ])>Estadísticas</a>
        @endcan

        @can('users.view')
            <a href="{{ route('admin.users.index') }}" @class([
                $navOn => request()->routeIs('admin.users.*'),
                $navOff => ! request()->routeIs('admin.users.*'),
            ])>Usuarios</a>
        @endcan

        <a href="{{ route('home') }}" class="{{ $navOff }} max-lg:hidden">Sitio público</a>
    </nav>

    <div class="hidden border-t border-primary/10 px-4 py-4 lg:block">
        <p class="truncate text-sm font-semibold text-secondary">{{ auth()->user()->name }}</p>
        <p class="truncate text-xs text-secondary">{{ auth()->user()->email }}</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="text-xs font-semibold text-primary underline-offset-2 hover:underline">
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>
