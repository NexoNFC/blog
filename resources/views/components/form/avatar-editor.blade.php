<div
    {{ $attributes->class(['avatar-editor']) }}
    x-data="avatarEditor"
    @dropzone-preview.window="load($event.detail)"
    @avatar-editor-load.window="load($event.detail)"
>
    <div class="avatar-editor__stage-wrap">
        <div
            class="avatar-editor__stage"
            :class="{ 'is-dragging': dragging }"
            @pointerdown.prevent="startDrag($event)"
            @pointermove.prevent="onDrag($event)"
            @pointerup.prevent="endDrag()"
            @pointercancel.prevent="endDrag()"
            @wheel.prevent="onWheel($event)"
        >
            <div class="avatar-editor__frame" :style="`width: ${viewport}px; height: ${viewport}px;`">
                <img
                    x-show="url"
                    :src="url"
                    alt="Editor de foto de perfil"
                    class="avatar-editor__image"
                    :style="imageStyle()"
                    @load="onImageLoad($event)"
                    draggable="false"
                >
            </div>
        </div>
    </div>

    <p class="mt-2 truncate text-center text-xs font-semibold text-secondary-light" x-text="name"></p>
    <p class="mt-1 text-center text-xs text-secondary-light">Arrastra la imagen · rueda del mouse para zoom</p>

    <div class="avatar-editor__controls">
        <div class="avatar-editor__toolbar">
            <button type="button" class="avatar-editor__btn" @click="rotateLeft()" title="Girar a la izquierda" aria-label="Girar a la izquierda">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15 3 9m0 0 6-6M3 9h11a4 4 0 0 1 0 8h-1"/>
                </svg>
            </button>

            <button type="button" class="avatar-editor__btn" @click="zoomOut()" title="Alejar" aria-label="Alejar">−</button>

            <input
                type="range"
                class="avatar-editor__range"
                min="0"
                max="100"
                step="1"
                :value="Math.round(((scale - minScale) / Math.max(0.01, maxScale - minScale)) * 100)"
                @input="setScaleFromSlider($event.target.value)"
                aria-label="Zoom"
            >

            <button type="button" class="avatar-editor__btn" @click="zoomIn()" title="Acercar" aria-label="Acercar">+</button>

            <button type="button" class="avatar-editor__btn" @click="rotateRight()" title="Girar a la derecha" aria-label="Girar a la derecha">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 15 6-6m0 0-6-6m6 6H10a4 4 0 1 0 0 8h1"/>
                </svg>
            </button>
        </div>

        <button type="button" class="avatar-editor__reset" @click="resetTransform()">
            Restablecer a original
        </button>
    </div>

    <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
        <x-ui.button
            type="button"
            variant="secondary"
            class="min-w-[9.5rem] justify-center"
            @click="$dispatch('avatar-preview-cancel'); $dispatch('close-modal', 'avatar-preview')"
        >
            Cancelar
        </x-ui.button>
        <x-ui.button
            type="button"
            class="min-w-[9.5rem] justify-center !rounded-2xl"
            @click="confirm()"
            x-bind:disabled="exporting || !url"
        >
            <span x-show="!exporting">Usar esta foto</span>
            <span x-cloak x-show="exporting">Preparando...</span>
        </x-ui.button>
    </div>
</div>
