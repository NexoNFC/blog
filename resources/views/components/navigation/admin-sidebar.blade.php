<aside class="border-b border-white/10 bg-secondary/95 text-white backdrop-blur-xl lg:flex lg:w-64 lg:shrink-0 lg:flex-col lg:border-b-0 lg:border-r lg:border-white/10">
    <div class="flex items-center justify-between px-5 py-5">
        <div>
            <p class="font-serif text-xl font-bold">FESC</p>
            <p class="text-xs text-white/60">Panel administrativo</p>
        </div>
        <a href="{{ route('home') }}" class="text-xs text-white/70 underline-offset-2 hover:text-white hover:underline lg:hidden">Ver sitio</a>
    </div>

    <nav class="flex gap-1 overflow-x-auto px-3 pb-4 text-sm lg:flex-1 lg:flex-col lg:overflow-visible" aria-label="Administración">
        <a href="{{ route('admin.dashboard') }}" @class([
            'rounded-xl px-3 py-2.5 whitespace-nowrap transition',
            'bg-white/15 text-white shadow-sm backdrop-blur-sm' => request()->routeIs('admin.dashboard'),
            'text-white/75 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.dashboard'),
        ])>Dashboard</a>

        @can('news.view')
            <a href="{{ route('admin.news.index') }}" @class([
                'rounded-xl px-3 py-2.5 whitespace-nowrap transition',
                'bg-white/15 text-white shadow-sm backdrop-blur-sm' => request()->routeIs('admin.news.*'),
                'text-white/75 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.news.*'),
            ])>Noticias</a>
        @endcan

        @can('categories.view')
            <a href="{{ route('admin.categories.index') }}" @class([
                'rounded-xl px-3 py-2.5 whitespace-nowrap transition',
                'bg-white/15 text-white shadow-sm backdrop-blur-sm' => request()->routeIs('admin.categories.*'),
                'text-white/75 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.categories.*'),
            ])>Categorías</a>
        @endcan

        @can('nfc.view')
            <a href="{{ route('admin.nfc.index') }}" @class([
                'rounded-xl px-3 py-2.5 whitespace-nowrap transition',
                'bg-white/15 text-white shadow-sm backdrop-blur-sm' => request()->routeIs('admin.nfc.*'),
                'text-white/75 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.nfc.*'),
            ])>Puntos NFC</a>
        @endcan

        @can('statistics.view')
            <a href="{{ route('admin.statistics.index') }}" @class([
                'rounded-xl px-3 py-2.5 whitespace-nowrap transition',
                'bg-white/15 text-white shadow-sm backdrop-blur-sm' => request()->routeIs('admin.statistics.*'),
                'text-white/75 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.statistics.*'),
            ])>Estadísticas</a>
        @elsecan('statistics.view-content')
            <a href="{{ route('admin.statistics.index') }}" @class([
                'rounded-xl px-3 py-2.5 whitespace-nowrap transition',
                'bg-white/15 text-white shadow-sm backdrop-blur-sm' => request()->routeIs('admin.statistics.*'),
                'text-white/75 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.statistics.*'),
            ])>Estadísticas</a>
        @endcan

        @can('users.view')
            <a href="{{ route('admin.users.index') }}" @class([
                'rounded-xl px-3 py-2.5 whitespace-nowrap transition',
                'bg-white/15 text-white shadow-sm backdrop-blur-sm' => request()->routeIs('admin.users.*'),
                'text-white/75 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.users.*'),
            ])>Usuarios</a>
        @endcan

        <a href="{{ route('home') }}" class="rounded-xl px-3 py-2.5 text-white/60 transition hover:bg-white/10 hover:text-white max-lg:hidden">Sitio público</a>
    </nav>

    <div class="hidden border-t border-white/10 px-5 py-4 lg:block">
        <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
        <p class="truncate text-xs text-white/50">{{ auth()->user()->email }}</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="text-xs font-semibold text-white/70 underline-offset-2 hover:text-white hover:underline">
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>
