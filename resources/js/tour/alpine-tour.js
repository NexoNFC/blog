export function createNfcTourViewerState(config = {}) {
    const initialMarker = Array.isArray(config.hotspots) && config.hotspots[0]
        ? config.hotspots[0]
        : null;

    return {
        imageUrl: config.imageUrl || '',
        hotspots: Array.isArray(config.hotspots) ? config.hotspots : [],
        editMode: Boolean(config.editMode),
        selected: null,
        hasPreviewImage: Boolean(config.imageUrl),
        draft: {
            theta: initialMarker ? String(initialMarker.theta) : '',
            phi: initialMarker ? String(initialMarker.phi) : '',
        },
        viewer: null,

        async boot() {
            if (this.viewer || ! this.$refs.canvas) {
                return;
            }

            const { createViewer360 } = await import('./viewer360');

            await this.$nextTick();

            await new Promise((resolve) => requestAnimationFrame(resolve));

            if (! this.$refs.canvas || this.viewer) {
                return;
            }

            this.viewer = createViewer360({
                container: this.$refs.canvas,
                imageUrl: this.imageUrl,
                hotspots: this.hotspots,
                editMode: this.editMode,
                onHotspotClick: (hotspot) => {
                    this.selected = hotspot;
                },
                onAddHotspot: ({ theta, phi }) => {
                    this.placeMarker(theta, phi);
                },
            });
        },

        placeMarker(theta, phi) {
            // Reasignar el objeto para que Alpine refresque :value de los hidden.
            this.draft = {
                theta: Number(theta).toFixed(4),
                phi: Number(phi).toFixed(4),
            };

            const marker = {
                id: 'nfc-marker',
                title: config.markerLabel || 'Tarjeta NFC',
                description: 'Aquí está ubicada la tarjeta NFC.',
                theta: Number(theta),
                phi: Number(phi),
            };

            this.hotspots = [marker];
            this.viewer?.setHotspots(this.hotspots);
            this.viewer?.setPreview(null);
            this.viewer?.lookAtHotspot(marker);
        },

        clearMarker() {
            this.draft = { theta: '', phi: '' };
            this.hotspots = [];
            this.viewer?.setHotspots([]);
            this.viewer?.setPreview(null);
        },

        onPanoramaPreview(detail = {}) {
            const url = typeof detail.url === 'string' ? detail.url : '';

            if (! url) {
                return;
            }

            this.imageUrl = url;
            this.hasPreviewImage = true;

            this.$nextTick(() => {
                requestAnimationFrame(async () => {
                    if (! this.viewer && this.$refs.canvas) {
                        await this.boot();

                        return;
                    }

                    this.viewer?.setImage(url);
                });
            });
        },

        destroy() {
            this.viewer?.destroy();
            this.viewer = null;
        },

        closeSelected() {
            this.selected = null;
        },
    };
}
