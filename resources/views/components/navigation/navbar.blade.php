@php
    $home = route('home');
    $link = 'nav-link block rounded-full px-3 py-2 text-sm font-semibold transition hover:bg-primary-soft md:py-1.5';
    $linkActive = 'nav-link active block rounded-full bg-primary-soft px-3 py-2 text-sm font-semibold md:py-1.5';
@endphp

<header class="nav-shell fixed inset-x-0 top-0 z-50">
    <nav class="nav-glass" aria-label="Principal">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ $home }}" class="flex min-w-0 items-center gap-3">
                <span class="inline-flex h-9 shrink-0 items-center justify-center rounded-xl bg-primary px-2.5 text-[0.7rem] font-bold tracking-[0.14em] text-white shadow-[0_8px_16px_rgb(200_16_46_/_0.28)]">
                    FESC
                </span>
                <span class="min-w-0">
                    <span class="block text-sm font-bold leading-none tracking-tight text-secondary">Información</span>
                    <span class="mt-1 block text-xs font-medium leading-none text-secondary">en campus</span>
                </span>
            </a>

            <div class="flex items-center gap-2 md:order-2">
                @auth
                    <x-ui.button href="{{ route('admin.dashboard') }}" size="sm" class="!rounded-2xl !px-4 !py-2 !shadow-[0_12px_28px_rgb(200_16_46_/_0.22)]">
                        Panel
                    </x-ui.button>
                @else
                    <x-ui.button href="{{ route('login') }}" size="sm" class="!rounded-2xl !px-4 !py-2 !shadow-[0_12px_28px_rgb(200_16_46_/_0.22)]">
                        Admin
                    </x-ui.button>
                @endauth

                <button
                    data-collapse-toggle="navbar-sticky"
                    type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-primary/15 bg-white text-secondary md:hidden hover:bg-primary-soft focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                    aria-controls="navbar-sticky"
                    aria-expanded="false"
                >
                    <span class="sr-only">Abrir menú principal</span>
                    <x-icon name="bars" outline class="h-6 w-6" />
                </button>
            </div>

            <div class="hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
                <ul class="mt-3 flex flex-col gap-1 rounded-2xl border border-primary/10 bg-white p-3 md:mt-0 md:flex-row md:items-center md:gap-1 md:border-0 md:bg-transparent md:p-0">
                    <li>
                        <a
                            href="{{ $home }}"
                            @class([
                                $linkActive => request()->routeIs('home'),
                                $link => ! request()->routeIs('home'),
                            ])
                            @if (request()->routeIs('home')) aria-current="page" @endif
                        >
                            Inicio
                        </a>
                    </li>
                    <li>
                        <a href="{{ $home }}#noticias" class="{{ $link }}">Noticias</a>
                    </li>
                    <li>
                        <a href="{{ $home }}#campus" class="{{ $link }}">Campus</a>
                    </li>
                    <li>
                        <a
                            href="{{ $home }}#nfc"
                            @class([
                                $linkActive => request()->routeIs('nfc.*'),
                                $link => ! request()->routeIs('nfc.*'),
                            ])
                        >
                            NFC
                        </a>
                    </li>
                    <li>
                        <a
                            href="https://www.fesc.edu.co/portal/"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="{{ $link }}"
                        >
                            Sitio oficial
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
