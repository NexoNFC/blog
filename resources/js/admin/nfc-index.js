const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

export const createAdminNfcIndexState = (config = {}) => ({
    points: Array.isArray(config.points) ? config.points : [],
    canManage: Boolean(config.canManage),
    busy: {},

    toast(detail) {
        window.dispatchEvent(new CustomEvent('toast', { detail }));
    },

    isBusy(code) {
        return Boolean(this.busy[code]);
    },

    setBusy(code, value) {
        this.busy = { ...this.busy, [code]: value };
    },

    statusTone(status) {
        return status === 'activo' ? 'admin-badge--success' : 'admin-badge--danger';
    },

    selectKey(point) {
        const optionIds = Array.isArray(point?.news_options)
            ? point.news_options.map((item) => item.id).join('-')
            : '';

        return [point?.code || '', point?.news_id ?? 'none', optionIds].join(':');
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
            const validation = payload.errors?.news_id?.[0];
            const message = validation
                || payload.message
                || payload.alert?.message
                || 'No se pudo completar la operación.';

            throw new Error(message);
        }

        return payload;
    },

    replacePoint(point) {
        if (! point?.code) {
            return;
        }

        const index = this.points.findIndex((item) => item.code === point.code);

        if (index === -1) {
            return;
        }

        this.points.splice(index, 1, {
            ...this.points[index],
            ...point,
        });
    },

    async associate(event, code) {
        const form = event?.target;

        if (! (form instanceof HTMLFormElement) || this.isBusy(code)) {
            return;
        }

        const formData = new FormData(form);
        const rawNewsId = formData.get('news_id');

        if (rawNewsId === '__browse__') {
            const browseUrl = form.querySelector('option[value="__browse__"]')?.dataset?.url;

            if (browseUrl) {
                window.location.assign(browseUrl);
            }

            return;
        }

        const newsId = rawNewsId === '' || rawNewsId === null
            ? null
            : Number(rawNewsId);

        this.setBusy(code, true);

        try {
            const payload = await this.request(form.action, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ news_id: Number.isFinite(newsId) ? newsId : null }),
            });

            if (payload.point) {
                this.replacePoint(payload.point);
            }

            this.toast({
                type: 'success',
                title: 'Listo',
                message: payload.message || 'La asociación del punto NFC se actualizó correctamente.',
            });
        } catch (error) {
            this.toast({
                type: 'danger',
                title: 'No se pudo asociar',
                message: error.message,
            });
        } finally {
            this.setBusy(code, false);
        }
    },
});
