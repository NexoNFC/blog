@php
    $navBase = 'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-secondary transition';
    $navOn = $navBase.' bg-primary-soft text-primary';
    $navOff = $navBase.' hover:bg-primary-soft hover:text-primary';
@endphp

<aside
    id="admin-sidebar"
    class="admin-sidebar fixed top-0 left-0 z-40 h-full w-64 -translate-x-full border-0 border-e border-primary/10 transition-transform duration-300 ease-in-out"
    :class="{ '!translate-x-0': open }"
    aria-label="Administración"
>
    <div class="flex h-full flex-col overflow-y-auto bg-white/95 px-3 pb-4 pt-[4.75rem] backdrop-blur-xl sm:pt-[5rem]">
        <a href="{{ route('admin.dashboard') }}" class="mb-5 flex items-center gap-3 rounded-xl px-2.5 py-1.5 sm:hidden">
            <span class="inline-flex h-9 shrink-0 items-center justify-center rounded-xl bg-primary px-2.5 text-[0.7rem] font-bold tracking-[0.14em] text-white shadow-[0_8px_16px_rgb(200_16_46_/_0.28)]">
                FESC
            </span>
            <span class="min-w-0">
                <span class="block text-sm font-bold leading-none tracking-tight text-secondary">Panel</span>
                <span class="mt-1 block text-xs font-medium leading-none text-secondary">administrativo</span>
            </span>
        </a>

        <ul class="space-y-1 font-medium">
            <li>
                <a
                    href="{{ route('admin.dashboard') }}"
                    @click="closeMobile()"
                    @class([
                        $navOn => request()->routeIs('admin.dashboard'),
                        $navOff => ! request()->routeIs('admin.dashboard'),
                    ])
                >
                    <x-icon name="home" class="h-5 w-5 shrink-0" />
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
                        <x-icon name="browsers" class="h-5 w-5 shrink-0" />
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
                        <x-icon name="apps-add" class="h-5 w-5 shrink-0" />
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
                        <x-icon name="degrees-360" class="h-5 w-5 shrink-0" />
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
                        <x-icon name="search" class="h-5 w-5 shrink-0" />
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
                        <x-icon name="search" class="h-5 w-5 shrink-0" />
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
                        <x-icon name="users-alt" class="h-5 w-5 shrink-0" />
                        <span>Usuarios</span>
                    </a>
                </li>
            @endcan

            <li>
                <a href="{{ route('home') }}" @click="closeMobile()" class="{{ $navOff }}">
                    <x-icon name="window-alt" class="h-5 w-5 shrink-0" />
                    <span>Sitio público</span>
                </a>
            </li>
        </ul>

        <div class="mt-auto border-t border-primary/10 px-2 pt-4">
            <p class="truncate text-sm font-semibold text-secondary">{{ auth()->user()->name }}</p>
            <p class="truncate text-xs text-secondary-light">{{ auth()->user()->email }}</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 text-xs font-semibold text-primary underline-offset-2 hover:underline">
                    <x-icon name="arrow-right-from-bracket" class="h-4 w-4" />
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</aside>
