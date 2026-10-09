const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

const emptyMeta = () => ({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
    from: null,
    to: null,
});

export const createAdminNfcAssociateState = (config = {}) => ({
    endpoint: config.endpoint || '',
    updateUrl: config.updateUrl || '',
    returnUrl: config.returnUrl || '',
    point: config.point || { news_id: null, news_title: null, code: '' },
    items: Array.isArray(config.data) ? config.data : [],
    meta: { ...emptyMeta(), ...(config.meta || {}) },
    query: typeof config.query === 'string' ? config.query : '',
    loading: false,
    searchTimer: null,

    get resultLabel() {
        const total = Number(this.meta.total || 0);
        const trimmed = this.query.trim();

        if (total === 0) {
            return trimmed === ''
                ? 'No hay noticias publicadas para asociar.'
                : 'Sin coincidencias para esta búsqueda.';
        }

        if (trimmed === '') {
            return `${total} noticia${total === 1 ? '' : 's'} publicada${total === 1 ? '' : 's'}`;
        }

        return `${total} coincidencia${total === 1 ? '' : 's'}`;
    },

    get pageItems() {
        const last = Number(this.meta.last_page || 1);
        const current = Number(this.meta.current_page || 1);

        if (last <= 1) {
            return [];
        }

        if (last <= 7) {
            return Array.from({ length: last }, (_, index) => index + 1);
        }

        const pages = new Set([1, last, current, current - 1, current + 1, current - 2, current + 2]);
        const sorted = [...pages].filter((page) => page >= 1 && page <= last).sort((a, b) => a - b);
        const items = [];

        sorted.forEach((page, index) => {
            if (index > 0 && page - sorted[index - 1] > 1) {
                items.push('...');
            }

            items.push(page);
        });

        return items;
    },

    clearSearch() {
        this.query = '';
        this.fetchPage(1);
    },

    onSearchInput() {
        clearTimeout(this.searchTimer);
        this.searchTimer = setTimeout(() => {
            this.fetchPage(1);
        }, 280);
    },

    goToPage(page) {
        const next = Number(page);

        if (! Number.isFinite(next) || next < 1 || next > Number(this.meta.last_page || 1) || this.loading) {
            return;
        }

        if (next === Number(this.meta.current_page || 1)) {
            return;
        }

        this.fetchPage(next);
    },

    async fetchPage(page = 1) {
        if (! this.endpoint || this.loading) {
            return;
        }

        this.loading = true;

        try {
            const url = new URL(this.endpoint, window.location.origin);
            url.searchParams.set('page', String(page));

            const trimmed = this.query.trim();

            if (trimmed !== '') {
                url.searchParams.set('q', trimmed);
            }

            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                credentials: 'same-origin',
            });

            const payload = await response.json().catch(() => ({}));

            if (! response.ok) {
                throw new Error(payload.message || 'No se pudo cargar el listado.');
            }

            this.items = Array.isArray(payload.data) ? payload.data : [];
            this.meta = { ...emptyMeta(), ...(payload.meta || {}) };

            if (payload.point) {
                this.point = { ...this.point, ...payload.point };
            }
        } catch (error) {
            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    type: 'danger',
                    title: 'No se pudo buscar',
                    message: error.message || 'Inténtalo de nuevo.',
                },
            }));
        } finally {
            this.loading = false;
        }
    },
});
