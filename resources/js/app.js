import Alpine from 'alpinejs';
import 'flowbite';

window.Alpine = Alpine;

Alpine.data('adminShell', () => {
    const desktopQuery = () => window.matchMedia('(min-width: 640px)');

    const readDesktopPreference = () => window.localStorage.getItem('admin-sidebar-open') !== '0';

    return {
        open: desktopQuery().matches ? readDesktopPreference() : false,
        isDesktop: desktopQuery().matches,

        init() {
            this.syncViewport();

            window.addEventListener('resize', () => {
                const wasDesktop = this.isDesktop;
                this.syncViewport();

                if (! wasDesktop && this.isDesktop) {
                    this.open = readDesktopPreference();
                }

                if (wasDesktop && ! this.isDesktop) {
                    this.open = false;
                }
            }, { passive: true });

            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && this.open && ! this.isDesktop) {
                    this.open = false;
                }
            });
        },

        syncViewport() {
            this.isDesktop = desktopQuery().matches;
        },

        toggle() {
            this.open = ! this.open;

            if (this.isDesktop) {
                window.localStorage.setItem('admin-sidebar-open', this.open ? '1' : '0');
            }
        },

        closeMobile() {
            if (! this.isDesktop) {
                this.open = false;
            }
        },
    };
});

Alpine.start();

const navGlass = document.querySelector('.nav-glass');

if (navGlass) {
    const syncNavSolid = () => {
        navGlass.classList.toggle('is-solid', window.scrollY > 12);
    };

    syncNavSolid();
    window.addEventListener('scroll', syncNavSolid, { passive: true });
}

document.addEventListener('pointermove', (event) => {
    const card = event.target instanceof Element ? event.target.closest('.glass-hover') : null;

    if (! card) {
        return;
    }

    const rect = card.getBoundingClientRect();
    card.style.setProperty('--glass-glow-x', `${event.clientX - rect.left}px`);
    card.style.setProperty('--glass-glow-y', `${event.clientY - rect.top}px`);
});
