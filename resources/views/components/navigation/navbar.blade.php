@php
    $home = route('home');
    $link = 'block rounded-sm px-3 py-2 font-sans text-sm text-secondary transition md:border-0 md:p-0 md:hover:bg-transparent md:hover:text-primary hover:bg-white/70';
    $linkActive = 'block rounded-sm px-3 py-2 font-sans text-sm font-semibold text-primary transition md:bg-transparent md:p-0';
@endphp

<nav
    class="fixed start-0 top-0 z-50 w-full border-b-4 border-primary-dark bg-gradient-to-r from-[#c8c8ca] via-[#f3f3f4] to-white"
    aria-label="Principal"
>
    <div class="relative mx-auto flex max-w-screen-xl flex-wrap items-center justify-between px-4 py-3.5 md:px-6">
        <a href="{{ $home }}" class="flex items-baseline space-x-2.5 rtl:space-x-reverse">
            <span class="self-center font-serif text-2xl font-bold tracking-tight whitespace-nowrap text-secondary">FESC</span>
            <span class="hidden font-sans text-sm font-normal text-[#9a9a9d] sm:inline">Información en campus</span>
        </a>

        <div class="flex items-center space-x-3 md:order-2 md:space-x-0 rtl:space-x-reverse">
            @auth
                <x-ui.button href="{{ route('admin.dashboard') }}" size="sm" class="!rounded-full !px-4 !py-1.5 !text-sm !font-bold !shadow-none">
                    Panel
                </x-ui.button>
            @else
                <x-ui.button href="{{ route('login') }}" size="sm" class="!rounded-full !px-4 !py-1.5 !text-sm !font-bold !shadow-none">
                    Admin
                </x-ui.button>
            @endauth

            <button
                data-collapse-toggle="navbar-sticky"
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-base text-sm text-secondary md:hidden hover:bg-white/70 hover:text-secondary focus:ring-2 focus:ring-muted focus:outline-none"
                aria-controls="navbar-sticky"
                aria-expanded="false"
            >
                <span class="sr-only">Abrir menú principal</span>
                <x-icon name="bars" outline class="h-6 w-6" />
            </button>
        </div>

        <div
            class="hidden w-full items-center justify-between md:absolute md:top-1/2 md:left-1/2 md:order-1 md:flex md:w-auto md:-translate-x-1/2 md:-translate-y-1/2"
            id="navbar-sticky"
        >
            <ul class="mt-4 flex flex-col rounded-base border border-default bg-white/80 p-4 font-medium md:mt-0 md:flex-row md:space-x-8 md:border-0 md:bg-transparent md:p-0 rtl:space-x-reverse">
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
