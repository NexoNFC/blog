<nav class="admin-topbar fixed inset-x-0 top-0 z-50 w-full border-b border-primary/10 bg-white/90 backdrop-blur-xl">
    <div class="px-3 py-3 sm:px-5">
        <div class="flex items-center justify-between gap-3">
            <div class="flex min-w-0 items-center gap-2">
                <button
                    type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-primary/15 bg-white text-secondary transition hover:bg-primary-soft focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
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
                    <span class="inline-flex h-9 shrink-0 items-center justify-center rounded-xl bg-primary px-2.5 text-[0.7rem] font-bold tracking-[0.14em] text-white shadow-[0_8px_16px_rgb(200_16_46_/_0.28)]">
                        FESC
                    </span>
                    <span class="hidden min-w-0 sm:block">
                        <span class="block text-sm font-bold leading-none tracking-tight text-secondary">Panel</span>
                        <span class="mt-1 block text-xs font-medium leading-none text-secondary">administrativo</span>
                    </span>
                </a>
            </div>

            <div class="relative flex items-center">
                <button
                    type="button"
                    id="admin-user-menu-button"
                    data-dropdown-toggle="admin-user-menu"
                    class="inline-flex items-center gap-2 rounded-xl border border-primary/15 bg-white px-2.5 py-1.5 text-left transition hover:bg-primary-soft focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                    aria-expanded="false"
                >
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="hidden min-w-0 sm:block">
                        <span class="block max-w-[10rem] truncate text-sm font-semibold text-secondary">{{ auth()->user()->name }}</span>
                        <span class="block max-w-[10rem] truncate text-xs text-secondary-light">{{ auth()->user()->email }}</span>
                    </span>
                    <x-icon name="caret-down" class="hidden h-3 w-3 text-secondary sm:block" />
                </button>

                <div
                    id="admin-user-menu"
                    class="z-50 hidden w-56 divide-y divide-primary/10 rounded-2xl border border-primary/10 bg-white shadow-lg"
                >
                    <div class="px-4 py-3">
                        <p class="truncate text-sm font-semibold text-secondary">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-secondary-light">{{ auth()->user()->email }}</p>
                    </div>
                    <ul class="p-2 text-sm font-medium text-secondary">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="block rounded-xl px-3 py-2 hover:bg-primary-soft hover:text-primary">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2 hover:bg-primary-soft hover:text-primary">
                                Perfil
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('home') }}" class="block rounded-xl px-3 py-2 hover:bg-primary-soft hover:text-primary">
                                Sitio público
                            </a>
                        </li>
                    </ul>
                    <div class="p-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-left text-sm font-semibold text-primary hover:bg-primary-soft"
                            >
                                <x-icon name="arrow-right-from-bracket" class="h-4 w-4" />
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
