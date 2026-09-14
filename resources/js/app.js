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

Alpine.data('fancySelect', () => ({
    open: false,
    value: '',
    label: '',
    disabled: false,
    options: [],
    menuStyle: {},
    onDocumentClick: null,
    onViewportChange: null,

    init() {
        const select = this.$refs.select;

        if (! (select instanceof HTMLSelectElement)) {
            return;
        }

        this.disabled = select.disabled;
        this.readOptions();
        this.value = select.value;
        this.syncLabel();

        this.$watch('value', (next) => {
            if (select.value !== next) {
                select.value = next;
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }

            this.syncLabel();
        });

        select.addEventListener('change', () => {
            if (this.value !== select.value) {
                this.value = select.value;
            }
        });

        this.onDocumentClick = (event) => {
            if (! this.open) {
                return;
            }

            const target = event.target;

            if (! (target instanceof Node)) {
                return;
            }

            if (this.$refs.trigger?.contains(target) || this.$refs.menu?.contains(target)) {
                return;
            }

            this.close();
        };

        this.onViewportChange = () => {
            if (this.open) {
                this.positionMenu();
            }
        };

        document.addEventListener('click', this.onDocumentClick);
        window.addEventListener('resize', this.onViewportChange, { passive: true });
        window.addEventListener('scroll', this.onViewportChange, { passive: true, capture: true });
    },

    destroy() {
        if (this.onDocumentClick) {
            document.removeEventListener('click', this.onDocumentClick);
        }

        if (this.onViewportChange) {
            window.removeEventListener('resize', this.onViewportChange);
            window.removeEventListener('scroll', this.onViewportChange, true);
        }
    },

    readOptions() {
        const select = this.$refs.select;

        this.options = Array.from(select.options).map((option) => ({
            value: option.value,
            label: option.textContent?.trim() || option.value,
            disabled: option.disabled,
        }));
    },

    syncLabel() {
        const match = this.options.find((option) => option.value === this.value);
        this.label = match?.label || 'Seleccionar';
    },

    toggle() {
        if (this.disabled) {
            return;
        }

        if (this.open) {
            this.close();

            return;
        }

        this.open = true;
        this.$nextTick(() => this.positionMenu());
    },

    close() {
        this.open = false;
    },

    choose(option) {
        if (option.disabled) {
            return;
        }

        this.value = option.value;
        this.close();
    },

    positionMenu() {
        const trigger = this.$refs.trigger;

        if (! (trigger instanceof HTMLElement)) {
            return;
        }

        const rect = trigger.getBoundingClientRect();
        const viewportPadding = 8;
        const maxHeight = Math.min(280, window.innerHeight - rect.bottom - viewportPadding);
        const width = Math.max(rect.width, 220);

        let left = rect.left;

        if (left + width > window.innerWidth - viewportPadding) {
            left = Math.max(viewportPadding, window.innerWidth - width - viewportPadding);
        }

        this.menuStyle = {
            position: 'fixed',
            top: `${rect.bottom + 6}px`,
            left: `${left}px`,
            width: `${width}px`,
            maxHeight: `${Math.max(120, maxHeight)}px`,
            zIndex: '80',
        };
    },
}));

Alpine.start();

const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const navGlass = document.querySelector('.nav-glass');

if (navGlass) {
    const syncNavSolid = () => {
        navGlass.classList.toggle('is-solid', window.scrollY > 12);
    };

    syncNavSolid();
    window.addEventListener('scroll', syncNavSolid, { passive: true });
}

document.addEventListener('pointermove', (event) => {
    if (prefersReducedMotion()) {
        return;
    }

    const card = event.target instanceof Element ? event.target.closest('.glass-hover') : null;

    if (! card) {
        return;
    }

    const rect = card.getBoundingClientRect();
    card.style.setProperty('--glass-glow-x', `${event.clientX - rect.left}px`);
    card.style.setProperty('--glass-glow-y', `${event.clientY - rect.top}px`);
});

const initScrollReveal = () => {
    const reveals = Array.from(document.querySelectorAll('.reveal'));

    if (reveals.length === 0) {
        return;
    }

    if (prefersReducedMotion() || ! ('IntersectionObserver' in window)) {
        reveals.forEach((element) => element.classList.add('is-revealed'));

        return;
    }

    document.documentElement.classList.add('motion-ready');

    document.querySelectorAll('[data-reveal-stagger]').forEach((group) => {
        const step = Number(group.getAttribute('data-reveal-step') || 70);
        const items = group.querySelectorAll('.reveal');

        items.forEach((element, index) => {
            if (! element.style.getPropertyValue('--reveal-delay')) {
                element.style.setProperty('--reveal-delay', `${index * step}ms`);
            }
        });
    });

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (! entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-revealed');
                observer.unobserve(entry.target);
            });
        },
        {
            root: null,
            rootMargin: '0px 0px -8% 0px',
            threshold: 0.12,
        },
    );

    reveals.forEach((element) => observer.observe(element));
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initScrollReveal();
    });
} else {
    initScrollReveal();
}

const fescCoin = document.querySelector('[data-fesc-coin]');

if (fescCoin instanceof HTMLButtonElement) {
    let boostTimer;

    fescCoin.addEventListener('click', () => {
        if (prefersReducedMotion()) {
            return;
        }

        window.clearTimeout(boostTimer);
        fescCoin.classList.remove('is-boosting');
        void fescCoin.offsetWidth;
        fescCoin.classList.add('is-boosting');

        boostTimer = window.setTimeout(() => {
            fescCoin.classList.remove('is-boosting');
        }, 1800);
    });
}
