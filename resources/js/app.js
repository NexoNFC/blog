import Alpine from 'alpinejs';
import 'flowbite';
import { createAdminNewsIndexState } from './admin/news-index';
import { createToastHostState } from './admin/toast-host';
import { createNfcTourViewerState } from './tour/alpine-tour';

window.Alpine = Alpine;

Alpine.data('nfcTourViewer', (config = {}) => ({
    ...createNfcTourViewerState(config),

    init() {
        this.boot();
    },
}));

Alpine.data('toastHost', (initial = []) => createToastHostState(initial));

Alpine.data('adminNewsIndex', (config = {}) => createAdminNewsIndexState(config));

Alpine.data('adminShell', () => {
    const desktopQuery = () => window.matchMedia('(min-width: 640px)');

    const readDesktopPreference = () => window.localStorage.getItem('admin-sidebar-open') !== '0';

    return {
        open: desktopQuery().matches ? readDesktopPreference() : false,
        isDesktop: desktopQuery().matches,

        init() {
            this.syncViewport();
            this.syncShellClass();

            window.requestAnimationFrame(() => {
                document.documentElement.classList.add('admin-sidebar-ready');
            });

            window.addEventListener('resize', () => {
                const wasDesktop = this.isDesktop;
                this.syncViewport();

                if (! wasDesktop && this.isDesktop) {
                    this.open = readDesktopPreference();
                }

                if (wasDesktop && ! this.isDesktop) {
                    this.open = false;
                }

                this.syncShellClass();
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

        syncShellClass() {
            document.documentElement.classList.toggle('admin-sidebar-open', this.open && this.isDesktop);
        },

        toggle() {
            this.open = ! this.open;

            if (this.isDesktop) {
                window.localStorage.setItem('admin-sidebar-open', this.open ? '1' : '0');
            }

            this.syncShellClass();
        },

        closeMobile() {
            if (! this.isDesktop) {
                this.open = false;
            }
        },
    };
});

Alpine.data('nfcAssociateSearch', () => ({
    query: '',

    get normalizedQuery() {
        return this.query.trim().toLowerCase();
    },

    matches(text) {
        const query = this.normalizedQuery;

        if (query === '') {
            return true;
        }

        return String(text || '').includes(query);
    },

    get hasResults() {
        const rows = this.$root.querySelectorAll('[data-nfc-news-row]');

        if (rows.length === 0) {
            return true;
        }

        return Array.from(rows).some((row) => this.matches(row.dataset.search || ''));
    },

    get resultLabel() {
        const rows = this.$root.querySelectorAll('[data-nfc-news-row]');
        const total = rows.length;

        if (total === 0) {
            return '';
        }

        const visible = Array.from(rows).filter((row) => this.matches(row.dataset.search || '')).length;

        if (this.normalizedQuery === '') {
            return `${total} noticia${total === 1 ? '' : 's'} publicada${total === 1 ? '' : 's'}`;
        }

        return `${visible} de ${total} coincidencia${visible === 1 ? '' : 's'}`;
    },
}));

const positionUiTooltip = (anchor, placement = 'top') => {
    if (! (anchor instanceof HTMLElement)) {
        return {};
    }

    const rect = anchor.getBoundingClientRect();
    const gap = 10;
    const maxWidth = Math.min(22 * 16, window.innerWidth - 16);
    const left = Math.min(
        Math.max(8, rect.left + rect.width / 2),
        window.innerWidth - 8,
    );

    if (placement === 'bottom') {
        return {
            position: 'fixed',
            top: `${rect.bottom + gap}px`,
            left: `${left}px`,
            maxWidth: `${maxWidth}px`,
            transform: 'translateX(-50%)',
            zIndex: '90',
        };
    }

    return {
        position: 'fixed',
        top: `${rect.top - gap}px`,
        left: `${left}px`,
        maxWidth: `${maxWidth}px`,
        transform: 'translate(-50%, -100%)',
        zIndex: '90',
    };
};

Alpine.data('uiTooltip', (config = {}) => ({
    content: config.content || '',
    placement: config.placement || 'top',
    delay: Number(config.delay ?? 140),
    visible: false,
    style: {},
    showTimer: null,
    hideTimer: null,

    scheduleShow() {
        const text = String(this.content || '').trim();

        if (text === '') {
            return;
        }

        clearTimeout(this.hideTimer);
        clearTimeout(this.showTimer);
        this.showTimer = setTimeout(() => {
            this.visible = true;
            this.$nextTick(() => {
                this.style = positionUiTooltip(this.$el, this.placement);
            });
        }, this.delay);
    },

    scheduleHide() {
        clearTimeout(this.showTimer);
        clearTimeout(this.hideTimer);
        this.hideTimer = setTimeout(() => {
            this.visible = false;
        }, 80);
    },

    setContent(next) {
        this.content = String(next || '').trim();

        if (this.content === '') {
            this.visible = false;
        }
    },
}));

Alpine.data('fancySelect', () => ({
    open: false,
    value: '',
    label: '',
    disabled: false,
    options: [],
    menuStyle: {},
    tipVisible: false,
    tipStyle: {},
    tipTimer: null,
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

        this.$watch('open', (isOpen) => {
            if (isOpen) {
                this.hideTip();
            }
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

            if (this.tipVisible) {
                this.positionTip();
            }
        };

        document.addEventListener('click', this.onDocumentClick);
        window.addEventListener('resize', this.onViewportChange, { passive: true });
        window.addEventListener('scroll', this.onViewportChange, { passive: true, capture: true });
    },

    destroy() {
        clearTimeout(this.tipTimer);

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
            href: option.dataset.url || null,
        }));
    },

    syncLabel() {
        const match = this.options.find((option) => option.value === this.value);
        this.label = match?.label || 'Seleccionar';
    },

    tipText() {
        const text = String(this.label || '').trim();

        if (text === '' || text === 'Seleccionar' || text === 'Sin noticia') {
            return '';
        }

        return text;
    },

    scheduleTipShow() {
        if (this.open || this.disabled || this.tipText() === '') {
            return;
        }

        clearTimeout(this.tipTimer);
        this.tipTimer = setTimeout(() => {
            this.tipVisible = true;
            this.$nextTick(() => this.positionTip());
        }, 140);
    },

    hideTip() {
        clearTimeout(this.tipTimer);
        this.tipVisible = false;
    },

    positionTip() {
        this.tipStyle = positionUiTooltip(this.$refs.trigger, 'top');
    },

    toggle() {
        if (this.disabled) {
            return;
        }

        if (this.open) {
            this.close();

            return;
        }

        this.hideTip();
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

        if (option.href) {
            this.close();
            window.location.assign(option.href);

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

Alpine.data('fileDropzone', (config = {}) => ({
    dragging: false,
    fileName: '',
    payloadData: '',
    accept: config.accept || 'image/jpeg,image/png,image/webp',
    maxSizeMb: Number(config.maxSizeMb || 2),
    previewEvent: config.previewEvent || 'dropzone-preview',
    payloadName: config.payloadName || null,

    init() {
        const input = this.$refs.input;

        if (input instanceof HTMLInputElement && input.files?.[0]) {
            this.fileName = input.files[0].name;
        }
    },

    onDragOver() {
        this.dragging = true;
    },

    onDragLeave() {
        this.dragging = false;
    },

    onDrop(event) {
        this.dragging = false;
        const file = event.dataTransfer?.files?.[0] ?? null;
        this.applyFile(file);
    },

    onChange(event) {
        const file = event.target.files?.[0] ?? null;
        this.applyFile(file);
    },

    async applyFile(file) {
        const input = this.$refs.input;

        if (! (input instanceof HTMLInputElement)) {
            return;
        }

        if (! file) {
            this.clear();
            return;
        }

        if (! this.isAccepted(file)) {
            this.clear();
            this.$dispatch('dropzone-error', {
                message: 'El archivo debe ser una imagen JPG, PNG o WEBP.',
            });
            return;
        }

        if (file.size > this.maxSizeMb * 1024 * 1024) {
            this.clear();
            this.$dispatch('dropzone-error', {
                message: `La imagen no puede superar ${this.maxSizeMb} MB.`,
            });
            return;
        }

        if (this.payloadName) {
            try {
                this.payloadData = await this.readAsDataUrl(file);
                this.fileName = file.name;
                input.value = '';
            } catch (error) {
                this.clear();
                this.$dispatch('dropzone-error', {
                    message: 'No fue posible leer la imagen seleccionada.',
                });
                return;
            }
        } else {
            this.setInputFile(file);
        }

        this.$dispatch(this.previewEvent, {
            name: file.name,
            url: URL.createObjectURL(file),
            size: file.size,
        });
    },

    readAsDataUrl(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(String(reader.result || ''));
            reader.onerror = () => reject(reader.error || new Error('read failed'));
            reader.readAsDataURL(file);
        });
    },

    setInputFile(file) {
        const input = this.$refs.input;

        if (! (input instanceof HTMLInputElement)) {
            return;
        }

        const transfer = new DataTransfer();
        transfer.items.add(file);
        input.files = transfer.files;
        this.fileName = file.name;
    },

    replaceFile(file) {
        if (! (file instanceof File)) {
            return;
        }

        this.applyFile(file);
    },

    isAccepted(file) {
        const accepted = this.accept
            .split(',')
            .map((item) => item.trim().toLowerCase())
            .filter(Boolean);

        if (accepted.length === 0) {
            return true;
        }

        const type = (file.type || '').toLowerCase();
        const extension = `.${(file.name.split('.').pop() || '').toLowerCase()}`;

        return accepted.some((rule) => {
            if (rule.endsWith('/*')) {
                return type.startsWith(rule.slice(0, -1));
            }

            if (rule.startsWith('.')) {
                return extension === rule;
            }

            return type === rule;
        });
    },

    clear() {
        const input = this.$refs.input;

        if (input instanceof HTMLInputElement) {
            input.value = '';
        }

        this.fileName = '';
        this.payloadData = '';
        this.dragging = false;
    },
}));

Alpine.data('avatarEditor', () => ({
    url: null,
    name: '',
    image: null,
    naturalW: 0,
    naturalH: 0,
    viewport: 240,
    scale: 1,
    minScale: 1,
    maxScale: 3,
    rotation: 0,
    offsetX: 0,
    offsetY: 0,
    dragging: false,
    lastX: 0,
    lastY: 0,
    exporting: false,

    load(detail) {
        this.url = detail.url;
        this.name = detail.name || 'avatar.jpg';
        this.image = null;
        this.naturalW = 0;
        this.naturalH = 0;
        this.scale = 1;
        this.minScale = 1;
        this.rotation = 0;
        this.offsetX = 0;
        this.offsetY = 0;
        this.dragging = false;
        this.exporting = false;
    },

    onImageLoad(event) {
        const img = event.target;

        if (! (img instanceof HTMLImageElement)) {
            return;
        }

        this.image = img;
        this.naturalW = img.naturalWidth;
        this.naturalH = img.naturalHeight;
        this.resetTransform();
    },

    coverScale() {
        if (! this.naturalW || ! this.naturalH) {
            return 1;
        }

        const rotated = this.rotation % 180 === 90;
        const width = rotated ? this.naturalH : this.naturalW;
        const height = rotated ? this.naturalW : this.naturalH;

        return Math.max(this.viewport / width, this.viewport / height);
    },

    displayedSize() {
        const rotated = this.rotation % 180 === 90;
        const width = (rotated ? this.naturalH : this.naturalW) * this.scale;
        const height = (rotated ? this.naturalW : this.naturalH) * this.scale;

        return { width, height };
    },

    offsetLimits() {
        if (! this.naturalW || ! this.naturalH) {
            return { maxX: 0, maxY: 0 };
        }

        const { width, height } = this.displayedSize();
        const radius = this.viewport / 2;

        return {
            maxX: Math.max(0, (width / 2) - radius),
            maxY: Math.max(0, (height / 2) - radius),
        };
    },

    clampOffset() {
        const { maxX, maxY } = this.offsetLimits();

        this.offsetX = Math.min(maxX, Math.max(-maxX, this.offsetX));
        this.offsetY = Math.min(maxY, Math.max(-maxY, this.offsetY));
    },

    refreshScaleBounds() {
        this.minScale = this.coverScale();
        this.maxScale = Math.max(this.minScale * 3, this.minScale + 0.5);
        this.scale = Math.min(this.maxScale, Math.max(this.minScale, this.scale));
        this.clampOffset();
    },

    resetTransform() {
        this.rotation = 0;
        this.minScale = this.coverScale();
        this.maxScale = Math.max(this.minScale * 3, this.minScale + 0.5);
        this.scale = this.minScale;
        this.offsetX = 0;
        this.offsetY = 0;
    },

    imageStyle() {
        return {
            width: `${this.naturalW}px`,
            height: `${this.naturalH}px`,
            transform: `translate(-50%, -50%) translate(${this.offsetX}px, ${this.offsetY}px) rotate(${this.rotation}deg) scale(${this.scale})`,
        };
    },

    setScale(nextScale) {
        this.scale = Math.min(this.maxScale, Math.max(this.minScale, Number(nextScale)));
        this.clampOffset();
    },

    setScaleFromSlider(value) {
        const ratio = Number(value) / 100;
        this.setScale(this.minScale + (ratio * (this.maxScale - this.minScale)));
    },

    zoomIn() {
        this.setScale(Number((this.scale + 0.1).toFixed(2)));
    },

    zoomOut() {
        this.setScale(Number((this.scale - 0.1).toFixed(2)));
    },

    onWheel(event) {
        if (event.deltaY < 0) {
            this.zoomIn();
        } else {
            this.zoomOut();
        }
    },

    rotateLeft() {
        this.rotation = (((this.rotation - 90) % 360) + 360) % 360;
        this.refreshScaleBounds();
    },

    rotateRight() {
        this.rotation = (((this.rotation + 90) % 360) + 360) % 360;
        this.refreshScaleBounds();
    },

    startDrag(event) {
        if (event.button !== undefined && event.button !== 0) {
            return;
        }

        this.dragging = true;
        this.lastX = event.clientX;
        this.lastY = event.clientY;
        event.currentTarget?.setPointerCapture?.(event.pointerId);
    },

    onDrag(event) {
        if (! this.dragging) {
            return;
        }

        this.offsetX += event.clientX - this.lastX;
        this.offsetY += event.clientY - this.lastY;
        this.lastX = event.clientX;
        this.lastY = event.clientY;
        this.clampOffset();
    },

    endDrag() {
        this.dragging = false;
        this.clampOffset();
    },

    async exportBlob() {
        if (! this.image || ! this.naturalW || ! this.naturalH) {
            return null;
        }

        this.clampOffset();

        const size = 512;
        const canvas = document.createElement('canvas');
        canvas.width = size;
        canvas.height = size;

        const ctx = canvas.getContext('2d');

        if (! ctx) {
            return null;
        }

        const ratio = size / this.viewport;

        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, size, size);
        ctx.translate(size / 2 + this.offsetX * ratio, size / 2 + this.offsetY * ratio);
        ctx.rotate((this.rotation * Math.PI) / 180);
        ctx.scale(this.scale * ratio, this.scale * ratio);
        ctx.drawImage(this.image, -this.naturalW / 2, -this.naturalH / 2, this.naturalW, this.naturalH);

        return await new Promise((resolve) => {
            canvas.toBlob((blob) => resolve(blob), 'image/jpeg', 0.92);
        });
    },

    async confirm() {
        if (this.exporting) {
            return;
        }

        this.exporting = true;

        try {
            const blob = await this.exportBlob();

            if (! blob) {
                this.$dispatch('dropzone-error', {
                    message: 'No fue posible preparar la foto de perfil.',
                });
                return;
            }

            const baseName = (this.name || 'avatar').replace(/\.[^.]+$/, '');
            const file = new File([blob], `${baseName}.jpg`, { type: 'image/jpeg' });
            const url = URL.createObjectURL(blob);

            this.$dispatch('avatar-preview-confirm', {
                url,
                file,
                name: file.name,
            });
            this.$dispatch('close');
        } finally {
            this.exporting = false;
        }
    },
}));

Alpine.start();

const autoGrowTextarea = (textarea) => {
    if (! (textarea instanceof HTMLTextAreaElement)) {
        return;
    }

    textarea.style.height = 'auto';
    const maxHeight = Number.parseFloat(window.getComputedStyle(textarea).maxHeight) || Number.POSITIVE_INFINITY;
    const nextHeight = Math.min(textarea.scrollHeight, maxHeight);
    textarea.style.height = `${nextHeight}px`;
    textarea.style.overflowY = textarea.scrollHeight > maxHeight ? 'auto' : 'hidden';
};

const bindAutoGrowTextareas = (root = document) => {
    root.querySelectorAll('textarea.form-control').forEach((textarea) => {
        if (textarea.dataset.autoGrowBound === '1') {
            return;
        }

        textarea.dataset.autoGrowBound = '1';
        autoGrowTextarea(textarea);
        textarea.addEventListener('input', () => autoGrowTextarea(textarea));
    });
};

bindAutoGrowTextareas();
document.addEventListener('DOMContentLoaded', () => bindAutoGrowTextareas());

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

    const howItWorks = document.querySelector('#como-funciona');

    if (howItWorks instanceof HTMLElement) {
        const syncTrackerVisibility = () => {
            const coinRect = nfcTracker.getBoundingClientRect();
            const sectionBottom = howItWorks.getBoundingClientRect().bottom;
            const pastSection = sectionBottom <= coinRect.top;

            nfcTracker.classList.toggle('is-hidden-by-nfc', pastSection);
            nfcTracker.setAttribute('aria-hidden', pastSection ? 'true' : 'false');

            if (pastSection) {
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
