@props([
    'name',
    'id' => null,
    'accept' => 'image/jpeg,image/png,image/webp',
    'maxSizeMb' => 2,
    'hint' => 'JPG, PNG o WEBP. Máximo 2 MB.',
    'title' => 'Haz clic para subir',
    'subtitle' => 'o arrastra y suelta la imagen aquí',
    'previewEvent' => 'dropzone-preview',
])

@php
    $id ??= $name;
@endphp

<div
    {{ $attributes->class(['w-full']) }}
    x-data="fileDropzone({
        accept: @js($accept),
        maxSizeMb: {{ (int) $maxSizeMb }},
        previewEvent: @js($previewEvent),
    })"
>
    <label
        for="{{ $id }}"
        class="file-dropzone"
        :class="{ 'is-dragging': dragging }"
        @dragover.prevent="onDragOver()"
        @dragleave.prevent="onDragLeave()"
        @drop.prevent="onDrop($event)"
    >
        <div class="file-dropzone__body">
            <span class="file-dropzone__icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h3a3 3 0 0 0 0-6h-.025a5.56 5.56 0 0 0 .025-.5A5.5 5.5 0 0 0 7.207 9.021C7.137 9.017 7.071 9 7 9a4 4 0 1 0 0 8h2.167M12 19v-9m0 0-2 2m2-2 2 2"/>
                </svg>
            </span>
            <p class="file-dropzone__title">
                <span class="font-bold text-primary">{{ $title }}</span>
                <span class="text-secondary-light"> {{ $subtitle }}</span>
            </p>
            <p class="file-dropzone__hint">{{ $hint }}</p>
            <p class="file-dropzone__file" x-show="fileName" x-text="fileName" x-cloak></p>
        </div>

        <input
            x-ref="input"
            id="{{ $id }}"
            name="{{ $name }}"
            type="file"
            class="sr-only"
            accept="{{ $accept }}"
            @change="onChange($event)"
        >
    </label>
</div>
