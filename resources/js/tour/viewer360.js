import * as THREE from 'three';
import { CSS2DObject, CSS2DRenderer } from 'three/addons/renderers/CSS2DRenderer.js';

const hotspotWorldPosition = (data, radius = 450) => {
    const phi = Number(data.phi) || 0;
    const theta = Number(data.theta) || 0;

    return {
        x: radius * Math.cos(phi) * Math.sin(theta),
        y: radius * Math.sin(phi),
        z: radius * Math.cos(phi) * Math.cos(theta),
    };
};

/** Convierte theta/phi del marcador a lon/lat de la cámara del viewer. */
const hotspotToViewAngles = (data) => {
    const { x, y, z } = hotspotWorldPosition(data, 1);
    const lon = THREE.MathUtils.radToDeg(Math.atan2(z, x));
    const lat = THREE.MathUtils.radToDeg(Math.asin(Math.max(-1, Math.min(1, y))));

    return { lon, lat };
};

/** Misma estructura HTML que <x-nfc.coin> / preview admin. */
const buildCoinElement = (data, { isPreview = false, onActivate = null } = {}) => {
    const root = document.createElement('button');
    root.type = 'button';
    root.className = isPreview
        ? 'tour-nfc-coin tour-nfc-coin tour-nfc-coin--preview'
        : 'tour-nfc-coin tour-nfc-coin';
    root.setAttribute('aria-label', data?.title || 'Tarjeta NFC');
    root.innerHTML = `
        <span class="fesc-coin tour-nfc-coin__body" aria-hidden="true">
            <span class="fesc-coin__layer fesc-coin__layer--back"></span>
            <span class="fesc-coin__layer fesc-coin__layer--back-middle"></span>
            <span class="fesc-coin__layer fesc-coin__layer--middle"></span>
            <span class="fesc-coin__layer fesc-coin__layer--front-middle"></span>
            <span class="fesc-coin__layer fesc-coin__layer--front"></span>
            <span class="fesc-coin__face fesc-coin__face--front">
                <strong>FESC</strong>
                <small>NFC</small>
            </span>
            <span class="fesc-coin__face fesc-coin__face--back">
                <strong>NFC</strong>
                <small>NFC</small>
            </span>
            <span class="fesc-coin__rim"></span>
        </span>
    `;

    let pointerX = 0;
    let pointerY = 0;

    root.addEventListener('pointerdown', (event) => {
        event.stopPropagation();
        pointerX = event.clientX;
        pointerY = event.clientY;
    });

    root.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        const moved = Math.abs(event.clientX - pointerX) > 8
            || Math.abs(event.clientY - pointerY) > 8;

        if (moved || typeof onActivate !== 'function') {
            return;
        }

        onActivate(data);
    });

    return root;
};

const disposeCoinMarker = (marker) => {
    if (! marker) {
        return;
    }

    const element = marker.element;

    if (element?.parentNode) {
        element.parentNode.removeChild(element);
    }
};

const createCoinMarker = (data, { isPreview = false, onActivate = null } = {}) => {
    const element = buildCoinElement(data, { isPreview, onActivate });
    const marker = new CSS2DObject(element);
    const position = hotspotWorldPosition(data, 420);

    marker.position.set(position.x, position.y, position.z);
    marker.userData = {
        isHotspot: true,
        isCoinMarker: true,
        data,
        isPreview,
        baseY: position.y,
        pulsePhase: Math.random() * Math.PI * 2,
    };

    return marker;
};

const animateHotspots = (hotspotsGroup, previewMarker, nowMs) => {
    const animateMarker = (marker) => {
        if (! marker?.userData?.isCoinMarker) {
            return;
        }

        const phase = Number(marker.userData.pulsePhase) || 0;
        const t = nowMs * 0.003;

        if (typeof marker.userData.baseY === 'number') {
            marker.position.y = marker.userData.baseY + Math.sin(t * 0.9 + phase) * 4;
        }
    };

    hotspotsGroup.children.forEach(animateMarker);

    if (previewMarker) {
        animateMarker(previewMarker);
    }
};

export function createViewer360(options = {}) {
    const container = options.container;
    const onHotspotClick = typeof options.onHotspotClick === 'function' ? options.onHotspotClick : () => {};
    const onAddHotspot = typeof options.onAddHotspot === 'function' ? options.onAddHotspot : () => {};

    if (! (container instanceof HTMLElement)) {
        return { destroy() {}, setImage() {}, setHotspots() {}, setEditMode() {}, setPreview() {} };
    }

    container.classList.add('tour-viewer');

    // No forzar position inline: en admin/público el contenedor ya es absolute
    // y un relative aquí deja una franja vacía (fondo gris) bajo el canvas.
    if (getComputedStyle(container).position === 'static') {
        container.style.position = 'relative';
    }

    let scene;
    let camera;
    let renderer;
    let labelRenderer;
    let sphere;
    let animationId = 0;
    let editMode = Boolean(options.editMode);
    let imageUrl = options.imageUrl || '';
    let hotspots = Array.isArray(options.hotspots) ? options.hotspots : [];
    let previewHotspot = options.previewHotspot || null;
    let previewMarker = null;

    const hotspotsGroup = new THREE.Group();
    const raycaster = new THREE.Raycaster();
    const mouse = new THREE.Vector2();
    const initialLook = hotspots[0]
        ? hotspotToViewAngles(hotspots[0])
        : { lon: 0, lat: 0 };

    const state = {
        lon: initialLook.lon,
        lat: initialLook.lat,
        phi: 0,
        theta: 0,
        pointerX: 0,
        pointerY: 0,
        lon0: 0,
        lat0: 0,
        isUserInteracting: false,
    };

    const coinActivate = (data) => {
        if (editMode) {
            return;
        }

        onHotspotClick(data);
    };

    const renderHotspots = () => {
        while (hotspotsGroup.children.length > 0) {
            const child = hotspotsGroup.children[0];
            hotspotsGroup.remove(child);
            disposeCoinMarker(child);
        }

        hotspots.forEach((hotspot) => {
            hotspotsGroup.add(createCoinMarker(hotspot, {
                onActivate: coinActivate,
            }));
        });
    };

    const updatePreview = () => {
        if (previewMarker) {
            scene.remove(previewMarker);
            disposeCoinMarker(previewMarker);
            previewMarker = null;
        }

        if (previewHotspot) {
            previewMarker = createCoinMarker(previewHotspot, { isPreview: true });
            scene.add(previewMarker);
        }
    };

    const loadTexture = (url) => {
        if (! sphere || ! url) {
            return;
        }

        const loader = new THREE.TextureLoader();
        loader.setCrossOrigin('anonymous');
        loader.load(
            url,
            (texture) => {
                texture.colorSpace = THREE.SRGBColorSpace;
                texture.minFilter = THREE.LinearFilter;
                texture.magFilter = THREE.LinearFilter;
                texture.needsUpdate = true;

                if (sphere.material.map) {
                    sphere.material.map.dispose();
                }

                sphere.material.map = texture;
                sphere.material.color.set(0xffffff);
                sphere.material.needsUpdate = true;
            },
            undefined,
            () => {
                console.warn('No se pudo cargar la imagen panorámica:', url);
            },
        );
    };

    const onWindowResize = () => {
        if (! camera || ! renderer || ! labelRenderer) {
            return;
        }

        const width = container.clientWidth || window.innerWidth;
        const height = container.clientHeight || window.innerHeight;
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height);
        labelRenderer.setSize(width, height);
    };

    const clientPoint = (event) => ({
        x: event.clientX ?? event.touches?.[0]?.clientX ?? 0,
        y: event.clientY ?? event.touches?.[0]?.clientY ?? 0,
    });

    const onPointerMove = (event) => {
        if (! state.isUserInteracting) {
            return;
        }

        const point = clientPoint(event);
        state.lon = (state.pointerX - point.x) * 0.15 + state.lon0;
        state.lat = (point.y - state.pointerY) * 0.15 + state.lat0;
    };

    const endPointer = (event) => {
        if (! state.isUserInteracting) {
            return;
        }

        const point = clientPoint(event);
        const movedLittle = Math.abs(point.x - state.pointerX) < 6
            && Math.abs(point.y - state.pointerY) < 6;

        if (movedLittle && camera) {
            if (editMode) {
                const rect = container.getBoundingClientRect();

                if (rect.width > 0 && rect.height > 0) {
                    mouse.x = ((point.x - rect.left) / rect.width) * 2 - 1;
                    mouse.y = -((point.y - rect.top) / rect.height) * 2 + 1;
                    raycaster.setFromCamera(mouse, camera);

                    const dir = raycaster.ray.direction;
                    const phi = Math.asin(Math.max(-1, Math.min(1, dir.y)));
                    const theta = Math.atan2(dir.x, dir.z);

                    onAddHotspot({ theta, phi });
                }
            } else {
                onHotspotClick(null);
            }
        }

        state.isUserInteracting = false;

        if (renderer?.domElement) {
            renderer.domElement.style.cursor = editMode ? 'crosshair' : 'grab';
        }

        document.removeEventListener('pointermove', onPointerMove);
        document.removeEventListener('pointerup', endPointer);
        document.removeEventListener('pointercancel', endPointer);
        document.removeEventListener('mousemove', onPointerMove);
        document.removeEventListener('mouseup', endPointer);
        document.removeEventListener('touchmove', onPointerMove);
        document.removeEventListener('touchend', endPointer);
    };

    const onPointerDown = (event) => {
        if (state.isUserInteracting) {
            return;
        }

        if (event.button != null && event.button !== 0) {
            return;
        }

        const point = clientPoint(event);

        state.isUserInteracting = true;
        state.pointerX = point.x;
        state.pointerY = point.y;
        state.lon0 = state.lon;
        state.lat0 = state.lat;

        if (renderer?.domElement) {
            renderer.domElement.style.cursor = editMode ? 'crosshair' : 'grabbing';
        }

        const supportsPointer = typeof window.PointerEvent === 'function';

        if (supportsPointer) {
            document.addEventListener('pointermove', onPointerMove, { passive: true });
            document.addEventListener('pointerup', endPointer);
            document.addEventListener('pointercancel', endPointer);
        } else {
            document.addEventListener('mousemove', onPointerMove, { passive: true });
            document.addEventListener('mouseup', endPointer);
            document.addEventListener('touchmove', onPointerMove, { passive: true });
            document.addEventListener('touchend', endPointer);
        }
    };

    const onWheel = (event) => {
        if (! camera) {
            return;
        }

        event.preventDefault();
        const fov = camera.fov + event.deltaY * 0.05;
        camera.fov = THREE.MathUtils.clamp(fov, 20, 90);
        camera.updateProjectionMatrix();
    };

    const update = () => {
        state.lat = Math.max(-85, Math.min(85, state.lat));
        state.phi = THREE.MathUtils.degToRad(90 - state.lat);
        state.theta = THREE.MathUtils.degToRad(state.lon);

        const x = 500 * Math.sin(state.phi) * Math.cos(state.theta);
        const y = 500 * Math.cos(state.phi);
        const z = 500 * Math.sin(state.phi) * Math.sin(state.theta);

        camera.lookAt(x, y, z);
        animateHotspots(hotspotsGroup, previewMarker, Date.now());
        renderer.render(scene, camera);
        labelRenderer.render(scene, camera);
    };

    const animate = () => {
        animationId = requestAnimationFrame(animate);
        update();
    };

    scene = new THREE.Scene();
    scene.add(hotspotsGroup);

    camera = new THREE.PerspectiveCamera(75, 1, 1, 1100);

    const geometry = new THREE.SphereGeometry(500, 60, 40);
    geometry.scale(-1, 1, 1);
    sphere = new THREE.Mesh(
        geometry,
        new THREE.MeshBasicMaterial({
            color: 0xffffff,
            side: THREE.FrontSide,
        }),
    );
    scene.add(sphere);

    renderer = new THREE.WebGLRenderer({
        antialias: true,
        alpha: false,
        preserveDrawingBuffer: true,
    });
    renderer.setClearColor(0x0b1220, 1);
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));

    const canvas = renderer.domElement;
    canvas.style.display = 'block';
    canvas.style.width = '100%';
    canvas.style.height = '100%';
    canvas.style.touchAction = 'none';
    canvas.style.cursor = editMode ? 'crosshair' : 'grab';
    canvas.style.userSelect = 'none';
    canvas.tabIndex = 0;
    container.appendChild(canvas);

    labelRenderer = new CSS2DRenderer();
    labelRenderer.domElement.className = 'tour-viewer__labels';
    labelRenderer.domElement.style.position = 'absolute';
    labelRenderer.domElement.style.inset = '0';
    labelRenderer.domElement.style.pointerEvents = 'none';
    container.appendChild(labelRenderer.domElement);

    renderHotspots();
    updatePreview();
    onWindowResize();
    loadTexture(imageUrl);
    animate();

    if (typeof window.PointerEvent === 'function') {
        canvas.addEventListener('pointerdown', onPointerDown);
    } else {
        canvas.addEventListener('mousedown', onPointerDown);
        canvas.addEventListener('touchstart', onPointerDown, { passive: true });
    }

    canvas.addEventListener('wheel', onWheel, { passive: false });
    window.addEventListener('resize', onWindowResize);

    return {
        setImage(url) {
            imageUrl = url || '';
            loadTexture(imageUrl);
        },
        setHotspots(next) {
            hotspots = Array.isArray(next) ? next : [];
            renderHotspots();
        },
        setPreview(next) {
            previewHotspot = next || null;
            updatePreview();
        },
        setEditMode(next) {
            editMode = Boolean(next);

            if (renderer?.domElement) {
                renderer.domElement.style.cursor = editMode ? 'crosshair' : 'grab';
            }
        },
        getViewState() {
            return {
                lon: state.lon,
                lat: state.lat,
                fov: camera?.fov ?? null,
                interacting: state.isUserInteracting,
            };
        },
        lookAtHotspot(data) {
            if (! data) {
                return;
            }

            const angles = hotspotToViewAngles(data);
            state.lon = angles.lon;
            state.lat = angles.lat;
        },
        destroy() {
            cancelAnimationFrame(animationId);
            canvas.removeEventListener('pointerdown', onPointerDown);
            canvas.removeEventListener('mousedown', onPointerDown);
            canvas.removeEventListener('touchstart', onPointerDown);
            canvas.removeEventListener('wheel', onWheel);
            document.removeEventListener('pointermove', onPointerMove);
            document.removeEventListener('pointerup', endPointer);
            document.removeEventListener('pointercancel', endPointer);
            document.removeEventListener('mousemove', onPointerMove);
            document.removeEventListener('mouseup', endPointer);
            document.removeEventListener('touchmove', onPointerMove);
            document.removeEventListener('touchend', endPointer);
            window.removeEventListener('resize', onWindowResize);

            while (hotspotsGroup.children.length > 0) {
                const child = hotspotsGroup.children[0];
                hotspotsGroup.remove(child);
                disposeCoinMarker(child);
            }

            if (previewMarker) {
                scene.remove(previewMarker);
                disposeCoinMarker(previewMarker);
                previewMarker = null;
            }

            if (labelRenderer?.domElement?.parentNode === container) {
                container.removeChild(labelRenderer.domElement);
            }

            if (renderer) {
                renderer.dispose();

                if (canvas.parentNode === container) {
                    container.removeChild(canvas);
                }
            }
        },
    };
}
