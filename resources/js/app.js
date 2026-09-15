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
const hasMotionOverride = () => document.documentElement.classList.contains('motion-override');

document.documentElement.classList.add('motion-override');

const restartSiteAnimations = () => {
    if (prefersReducedMotion() && ! hasMotionOverride()) {
        document.documentElement.classList.remove('animations-reset');

        return;
    }

    document.documentElement.classList.add('animations-reset');

    window.requestAnimationFrame(() => {
        window.requestAnimationFrame(() => {
            document.documentElement.classList.remove('animations-reset');
        });
    });
};

restartSiteAnimations();

window.addEventListener('pageshow', restartSiteAnimations);
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        restartSiteAnimations();
    }
});

const navGlass = document.querySelector('.nav-glass');

if (navGlass) {
    const syncNavSolid = () => {
        navGlass.classList.toggle('is-solid', window.scrollY > 12);
    };

    syncNavSolid();
    window.addEventListener('scroll', syncNavSolid, { passive: true });
}

document.addEventListener('pointermove', (event) => {
    if (prefersReducedMotion() && ! hasMotionOverride()) {
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

    if ((prefersReducedMotion() && ! hasMotionOverride()) || ! ('IntersectionObserver' in window)) {
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
const nfcTracker = document.querySelector('[data-nfc-tracker]');

if (fescCoin instanceof HTMLButtonElement) {
    let boostTimer;

    fescCoin.addEventListener('click', () => {
        if (prefersReducedMotion() && ! hasMotionOverride()) {
            document.documentElement.classList.add('motion-override');
            initScrollReveal();
            restartSiteAnimations();
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

if (nfcTracker instanceof HTMLElement) {
    // Garantiza anclaje al viewport aunque algún ancestro cree containing block.
    if (nfcTracker.parentElement !== document.body) {
        document.body.appendChild(nfcTracker);
    }

    const pinTrackerToViewport = () => {
        nfcTracker.style.setProperty('position', 'fixed', 'important');
        nfcTracker.style.setProperty('top', '5.75rem', 'important');
        nfcTracker.style.setProperty('right', '1.25rem', 'important');
        nfcTracker.style.setProperty('bottom', 'auto', 'important');
        nfcTracker.style.setProperty('left', 'auto', 'important');
        nfcTracker.style.setProperty('z-index', '60', 'important');
    };

    pinTrackerToViewport();
    window.addEventListener('scroll', pinTrackerToViewport, { passive: true });
    window.addEventListener('resize', pinTrackerToViewport, { passive: true });

    const syncLookAtPointer = (clientX, clientY) => {
        if (prefersReducedMotion() && ! hasMotionOverride()) {
            return;
        }

        const rect = nfcTracker.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;
        const deltaX = clientX - centerX;
        const deltaY = clientY - centerY;
        const angle = Math.atan2(deltaY, deltaX) * (180 / Math.PI) + 90;
        const maxTilt = 10;
        const reach = Math.max(rect.width, 180);
        const tiltX = Math.max(-1, Math.min(1, deltaY / reach)) * maxTilt;
        const tiltY = Math.max(-1, Math.min(1, deltaX / reach)) * maxTilt;
        const yaw = Math.max(-1, Math.min(1, deltaX / reach)) * 14;
        const pitch = -8 + Math.max(-1, Math.min(1, deltaY / reach)) * 6;

        nfcTracker.style.setProperty('--look-angle', `${angle}deg`);
        nfcTracker.style.setProperty('--look-rx', `${-tiltX}deg`);
        nfcTracker.style.setProperty('--look-ry', `${tiltY}deg`);
        nfcTracker.style.setProperty('--coin-yaw', `${yaw}deg`);
        nfcTracker.style.setProperty('--coin-pitch', `${pitch}deg`);
    };

    document.addEventListener('pointermove', (event) => {
        syncLookAtPointer(event.clientX, event.clientY);
    }, { passive: true });

    syncLookAtPointer(window.innerWidth * 0.35, window.innerHeight * 0.55);

    const nfcSection = document.querySelector('#nfc');

    if (nfcSection instanceof HTMLElement) {
        const syncTrackerVisibility = () => {
            const coinRect = nfcTracker.getBoundingClientRect();
            const sectionRect = nfcSection.getBoundingClientRect();

            // Solo se oculta si la moneda se solapa con #nfc (no cuando #nfc entra al viewport).
            const overlaps =
                coinRect.left < sectionRect.right
                && coinRect.right > sectionRect.left
                && coinRect.top < sectionRect.bottom
                && coinRect.bottom > sectionRect.top;

            nfcTracker.classList.toggle('is-hidden-by-nfc', overlaps);
            nfcTracker.setAttribute('aria-hidden', overlaps ? 'true' : 'false');

            if (overlaps) {
                nfcTracker.setAttribute('tabindex', '-1');
            } else {
                nfcTracker.removeAttribute('tabindex');
            }
        };

        syncTrackerVisibility();
        window.addEventListener('scroll', syncTrackerVisibility, { passive: true });
        window.addEventListener('resize', syncTrackerVisibility, { passive: true });
    }
}
