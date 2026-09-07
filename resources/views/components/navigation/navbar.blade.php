<header class="sticky top-0 z-30 border-b border-muted/80 bg-surface/90 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <a href="{{ route('home') }}" class="group flex min-w-0 items-baseline gap-2">
            <span class="font-serif text-2xl font-bold tracking-tight text-secondary transition group-hover:text-primary">FESC</span>
            <span class="hidden truncate text-sm text-secondary-light sm:inline">Información en campus</span>
        </a>

        <nav class="flex items-center gap-3 text-sm font-medium text-secondary sm:gap-5" aria-label="Principal">
            <a href="#noticias" class="hidden hover:text-primary sm:inline">Noticias</a>
            <a href="#campus" class="hidden hover:text-primary sm:inline">Campus</a>
            <a href="#nfc" class="hover:text-primary">NFC</a>
            <a href="https://www.fesc.edu.co/portal/" target="_blank" rel="noopener noreferrer" class="hidden text-secondary-light hover:text-primary md:inline">Sitio oficial</a>
            <a href="{{ route('admin.dashboard') }}" class="text-secondary-light hover:text-primary">Admin</a>
        </nav>
    </div>
    <div class="h-1 bg-primary" aria-hidden="true"></div>
</header>
