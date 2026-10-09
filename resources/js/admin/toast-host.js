let toastSeq = 0;

const timeoutFor = (type) => (type === 'danger' || type === 'warning' ? 8000 : 5000);

export const createToastHostState = (initial = []) => ({
    toasts: [],

    init() {
        (Array.isArray(initial) ? initial : []).forEach((toast) => this.push(toast));
    },

    push(detail = {}) {
        const type = detail.type === 'error' ? 'danger' : (detail.type || 'info');
        const messages = Array.isArray(detail.messages)
            ? detail.messages.filter(Boolean)
            : [detail.message].filter(Boolean);

        if (messages.length === 0 && ! detail.title) {
            return;
        }

        const id = detail.id || `toast-${Date.now()}-${++toastSeq}`;
        const timeout = Number(detail.timeout || timeoutFor(type));

        this.toasts.push({
            id,
            type,
            title: detail.title || null,
            messages,
            timeout,
            show: true,
        });

        if (timeout > 0) {
            window.setTimeout(() => this.dismiss(id), timeout);
        }
    },

    dismiss(id) {
        const toast = this.toasts.find((item) => item.id === id);

        if (! toast) {
            return;
        }

        toast.show = false;
        window.setTimeout(() => {
            this.toasts = this.toasts.filter((item) => item.id !== id);
        }, 200);
    },

    iconClass(type) {
        if (type === 'success') {
            return 'bg-success-soft text-success';
        }

        if (type === 'danger') {
            return 'bg-danger-soft text-danger';
        }

        if (type === 'warning') {
            return 'bg-warning-soft text-warning';
        }

        return 'bg-info-soft text-info';
    },
});
