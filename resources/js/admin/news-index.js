const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

export const createAdminNewsIndexState = (config = {}) => ({
    items: Array.isArray(config.items) ? config.items : [],
    lastRun: config.lastRun || null,
    aiConfigured: Boolean(config.aiConfigured),
    can: config.can || {},
    routes: config.routes || {},
    ingesting: false,
    busy: {},

    toast(detail) {
        window.dispatchEvent(new CustomEvent('toast', { detail }));
    },

    isBusy(slug) {
        return Boolean(this.busy[slug]);
    },

    setBusy(slug, value) {
        this.busy = { ...this.busy, [slug]: value };
    },

    statusTone(status) {
        if (status === 'publicado') {
            return 'admin-badge--success';
        }

        if (status === 'archivado') {
            return 'admin-badge--neutral';
        }

        return 'admin-badge--warning';
    },

    presentationTone(presentation) {
        return presentation === 'ai' ? 'admin-badge--info' : 'admin-badge--neutral';
    },

    presentationLabel(presentation) {
        return presentation === 'ai' ? 'IA' : 'Original';
    },

    async request(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
                ...(options.headers || {}),
            },
            credentials: 'same-origin',
        });

        const payload = await response.json().catch(() => ({}));

        if (! response.ok) {
            const message = payload.message
                || payload.alert?.message
                || 'No se pudo completar la operación.';

            throw new Error(message);
        }

        return payload;
    },

    replaceItem(news) {
        if (! news?.slug) {
            return;
        }

        const index = this.items.findIndex((item) => item.slug === news.slug);

        if (index === -1) {
            this.items = [news, ...this.items];

            return;
        }

        this.items.splice(index, 1, news);
    },

    async refresh() {
        if (! this.routes.index) {
            return;
        }

        const url = new URL(this.routes.index, window.location.origin);
        const current = new URL(window.location.href);

        if (current.searchParams.has('page')) {
            url.searchParams.set('page', current.searchParams.get('page'));
        }

        const payload = await this.request(url.toString(), { method: 'GET' });
        this.items = Array.isArray(payload.data) ? payload.data : [];
        this.lastRun = payload.last_run || this.lastRun;
        this.aiConfigured = payload.ai_configured ?? this.aiConfigured;
    },

    async ingest() {
        if (this.ingesting || ! this.routes.ingest) {
            return;
        }

        this.ingesting = true;

        try {
            const payload = await this.request(this.routes.ingest, { method: 'POST' });

            this.lastRun = payload.last_run || this.lastRun;

            if (Array.isArray(payload.data)) {
                this.items = payload.data;
                const url = new URL(window.location.href);
                url.searchParams.delete('page');
                window.history.replaceState({}, '', url.pathname + url.search);
            } else {
                await this.refresh();
            }

            this.toast({
                type: payload.type || 'success',
                title: payload.title || (payload.type === 'danger' ? 'No se pudieron traer las noticias' : 'Listo'),
                message: payload.message,
            });
        } catch (error) {
            this.toast({
                type: 'danger',
                title: 'No se pudieron traer las noticias',
                message: error.message,
            });
        } finally {
            this.ingesting = false;
        }
    },

    async publish(item) {
        if (! item?.urls?.publish || this.isBusy(item.slug)) {
            return;
        }

        this.setBusy(item.slug, true);

        try {
            const payload = await this.request(item.urls.publish, { method: 'POST' });
            this.replaceItem(payload.news || { ...item, status: 'publicado' });
            this.toast({
                type: 'success',
                title: 'Listo',
                message: payload.message || 'La noticia se aprobó y publicó correctamente.',
            });
        } catch (error) {
            this.toast({
                type: 'danger',
                title: 'No se pudo publicar',
                message: error.message,
            });
        } finally {
            this.setBusy(item.slug, false);
        }
    },

    async archive(item) {
        if (! item?.urls?.destroy || this.isBusy(item.slug)) {
            return;
        }

        if (! window.confirm('¿Desaprobar esta noticia? Se archivará y permanecerá en el histórico.')) {
            return;
        }

        this.setBusy(item.slug, true);

        try {
            const payload = await this.request(item.urls.destroy, { method: 'DELETE' });
            this.replaceItem(payload.news || { ...item, status: 'archivado', published_at: null });
            this.toast({
                type: 'success',
                title: 'Listo',
                message: payload.message || `La noticia «${item.title}» se desaprobó y permanece en el histórico.`,
            });
        } catch (error) {
            this.toast({
                type: 'danger',
                title: 'No se pudo desaprobar',
                message: error.message,
            });
        } finally {
            this.setBusy(item.slug, false);
        }
    },
});
