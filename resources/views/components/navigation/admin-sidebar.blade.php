@php
    $navBase = 'admin-nav-link group';
    $navOn = $navBase.' is-active';
    $navOff = $navBase;
@endphp

<aside
    id="admin-sidebar"
    class="admin-sidebar fixed top-0 left-0 z-40 h-full w-64 -translate-x-full border-0 border-e border-primary/10 transition-transform duration-300 ease-in-out"
    :class="{ '!translate-x-0': open }"
    aria-label="Administración"
>
    <div class="flex h-full flex-col overflow-y-auto px-3 pb-4 pt-[4.75rem] sm:pt-[5rem]">
        <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__brand mb-5 flex items-center gap-3 rounded-2xl px-2.5 py-2 sm:hidden">
            <span class="admin-brand-badge">FESC</span>
            <span class="min-w-0">
                <span class="block text-sm font-bold leading-none tracking-tight text-secondary">Panel</span>
                <span class="mt-1 block text-xs font-medium leading-none text-secondary-light">administrativo</span>
            </span>
        </a>

        <p class="admin-sidebar__section">Menú</p>

        <ul class="space-y-1">
            <li>
                <a
                    href="{{ route('admin.dashboard') }}"
                    @click="closeMobile()"
                    @class([
                        $navOn => request()->routeIs('admin.dashboard'),
                        $navOff => ! request()->routeIs('admin.dashboard'),
                    ])
                >
                    <span class="admin-nav-link__icon"><x-icon name="home" class="h-4 w-4" /></span>
                    <span>Dashboard</span>
                </a>
            </li>

            @can('news.view')
                <li>
                    <a
                        href="{{ route('admin.news.index') }}"
                        @click="closeMobile()"
                        @class([
                            $navOn => request()->routeIs('admin.news.*'),
                            $navOff => ! request()->routeIs('admin.news.*'),
                        ])
                    >
                        <span class="admin-nav-link__icon"><x-icon name="browsers" class="h-4 w-4" /></span>
                        <span>Noticias</span>
                    </a>
                </li>
            @endcan

            @can('categories.view')
                <li>
                    <a
                        href="{{ route('admin.categories.index') }}"
                        @click="closeMobile()"
                        @class([
                            $navOn => request()->routeIs('admin.categories.*'),
                            $navOff => ! request()->routeIs('admin.categories.*'),
                        ])
                    >
                        <span class="admin-nav-link__icon"><x-icon name="apps-add" class="h-4 w-4" /></span>
                        <span>Categorías</span>
                    </a>
                </li>
            @endcan

            @can('nfc.view')
                <li>
                    <a
                        href="{{ route('admin.nfc.index') }}"
                        @click="closeMobile()"
                        @class([
                            $navOn => request()->routeIs('admin.nfc.*'),
                            $navOff => ! request()->routeIs('admin.nfc.*'),
                        ])
                    >
                        <span class="admin-nav-link__icon"><x-icon name="nfc" class="h-4 w-4" /></span>
                        <span>Puntos NFC</span>
                    </a>
                </li>
            @endcan

            @can('statistics.view')
                <li>
                    <a
                        href="{{ route('admin.statistics.index') }}"
                        @click="closeMobile()"
                        @class([
                            $navOn => request()->routeIs('admin.statistics.*'),
                            $navOff => ! request()->routeIs('admin.statistics.*'),
                        ])
                    >
                        <span class="admin-nav-link__icon"><x-icon name="adjustments-vertical" class="h-4 w-4" /></span>
                        <span>Estadísticas</span>
                    </a>
                </li>
            @elsecan('statistics.view-content')
                <li>
                    <a
                        href="{{ route('admin.statistics.index') }}"
                        @click="closeMobile()"
                        @class([
                            $navOn => request()->routeIs('admin.statistics.*'),
                            $navOff => ! request()->routeIs('admin.statistics.*'),
                        ])
                    >
                        <span class="admin-nav-link__icon"><x-icon name="adjustments-vertical" class="h-4 w-4" /></span>
                        <span>Estadísticas</span>
                    </a>
                </li>
            @endcan

            @can('users.view')
                <li>
                    <a
                        href="{{ route('admin.users.index') }}"
                        @click="closeMobile()"
                        @class([
                            $navOn => request()->routeIs('admin.users.*'),
                            $navOff => ! request()->routeIs('admin.users.*'),
                        ])
                    >
                        <span class="admin-nav-link__icon"><x-icon name="users" class="h-4 w-4" /></span>
                        <span>Usuarios</span>
                    </a>
                </li>
            @endcan
        </ul>

        <p class="admin-sidebar__section mt-5">Accesos</p>
        <ul class="space-y-1">
            <li>
                <a href="{{ route('home') }}" @click="closeMobile()" class="{{ $navOff }}">
                    <span class="admin-nav-link__icon"><x-icon name="window-alt" class="h-4 w-4" /></span>
                    <span>Sitio público</span>
                </a>
            </li>
        </ul>

        <div class="admin-sidebar__footer mt-auto">
            <div class="flex items-center gap-3">
                <span class="admin-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold tracking-tight text-secondary">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-secondary-light">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="admin-sidebar__logout">
                    <x-icon name="logout" class="h-4 w-4" />
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</aside>
