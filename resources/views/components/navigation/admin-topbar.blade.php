<nav class="admin-topbar fixed inset-x-0 top-0 z-50 w-full">
    <div class="px-3 py-3 sm:px-5">
        <div class="flex items-center justify-between gap-3">
            <div class="flex min-w-0 items-center gap-2.5">
                <button
                    type="button"
                    class="admin-icon-btn"
                    @click="toggle()"
                    :aria-expanded="open.toString()"
                    aria-controls="admin-sidebar"
                >
                    <span class="sr-only" x-text="open ? 'Cerrar menú' : 'Abrir menú'"></span>
                    <svg
                        x-show="!open"
                        class="h-5 w-5"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h10"/>
                    </svg>
                    <svg
                        x-show="open"
                        x-cloak
                        class="h-5 w-5"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/>
                    </svg>
                </button>

                <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <span class="admin-brand-badge">FESC</span>
                    <span class="hidden min-w-0 sm:block">
                        <span class="block text-sm font-bold leading-none tracking-tight text-secondary">Panel</span>
                        <span class="mt-1 block text-xs font-medium leading-none text-secondary-light">administrativo</span>
                    </span>
                </a>
            </div>

            <div class="relative flex items-center">
                <button
                    type="button"
                    id="admin-user-menu-button"
                    data-dropdown-toggle="admin-user-menu"
                    class="admin-user-trigger"
                    aria-expanded="false"
                >
                    <span class="admin-avatar">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="hidden min-w-0 sm:block">
                        <span class="block max-w-[10rem] truncate text-sm font-bold tracking-tight text-secondary">{{ auth()->user()->name }}</span>
                        <span class="block max-w-[10rem] truncate text-xs text-secondary-light">{{ auth()->user()->email }}</span>
                    </span>
                    <x-icon name="caret-down" class="hidden h-4 w-4 shrink-0 text-secondary-light sm:block" />
                </button>

                <div
                    id="admin-user-menu"
                    class="admin-user-menu z-50 hidden w-56"
                >
                    <div class="px-4 py-3">
                        <p class="truncate text-sm font-bold tracking-tight text-secondary">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-secondary-light">{{ auth()->user()->email }}</p>
                    </div>
                    <ul class="p-2 text-sm font-semibold text-secondary">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="admin-user-menu__item">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.edit') }}" class="admin-user-menu__item">
                                Perfil
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('home') }}" class="admin-user-menu__item">
                                Sitio público
                            </a>
                        </li>
                    </ul>
                    <div class="border-t border-primary/10 p-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="admin-user-menu__item admin-user-menu__item--danger w-full text-left"
                            >
                                <x-icon name="logout" class="h-4 w-4" />
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
