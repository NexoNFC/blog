<aside class="border-b border-muted bg-secondary text-white lg:w-64 lg:shrink-0 lg:border-b-0 lg:border-r lg:border-muted">
    <div class="flex items-center justify-between px-5 py-5">
        <div>
            <p class="font-serif text-xl font-bold">FESC</p>
            <p class="text-xs text-white/60">Panel administrativo</p>
        </div>
        <a href="{{ route('home') }}" class="text-xs text-white/70 underline-offset-2 hover:text-white hover:underline lg:hidden">Ver sitio</a>
    </div>

    <nav class="flex gap-1 overflow-x-auto px-3 pb-4 text-sm lg:flex-col lg:overflow-visible" aria-label="Administración">
        <a href="{{ route('admin.dashboard') }}" @class([
            'rounded px-3 py-2.5 whitespace-nowrap transition',
            'bg-white/15 text-white' => request()->routeIs('admin.dashboard'),
            'text-white/75 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.dashboard'),
        ])>Dashboard</a>
        <a href="{{ route('admin.contents.index') }}" @class([
            'rounded px-3 py-2.5 whitespace-nowrap transition',
            'bg-white/15 text-white' => request()->routeIs('admin.contents.*'),
            'text-white/75 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.contents.*'),
        ])>Contenidos</a>
        <a href="{{ route('admin.nfc.index') }}" @class([
            'rounded px-3 py-2.5 whitespace-nowrap transition',
            'bg-white/15 text-white' => request()->routeIs('admin.nfc.*'),
            'text-white/75 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.nfc.*'),
        ])>Puntos NFC</a>
        <a href="{{ route('home') }}" class="rounded px-3 py-2.5 text-white/60 transition hover:bg-white/10 hover:text-white max-lg:hidden">Sitio público</a>
    </nav>

    <p class="hidden px-5 pb-5 text-xs text-white/40 lg:block">Maqueta sin autenticación</p>
</aside>
