@extends('layouts.admin')

@section('title', 'Vista 360° · '.$point->name)
@section('heading', $point->name)
@section('subtitle', 'Panorama del espacio y ubicación de la tarjeta NFC')

@section('actions')
    <x-ui.button href="{{ route('admin.nfc.index') }}" variant="secondary" class="!rounded-2xl">Volver a puntos NFC</x-ui.button>
    @if ($point->hasTour())
        <x-ui.button href="{{ route('nfc.tour', $point) }}" variant="secondary" class="!rounded-2xl">Probar vista 360°</x-ui.button>
    @endif
@endsection

@section('content')
    <div
        class="grid gap-5 xl:grid-cols-12"
        x-data="nfcTourViewer({
            imageUrl: @js($tour['panorama_url']),
            hotspots: @js($tour['marker'] ? [$tour['marker']] : []),
            editMode: true,
            markerLabel: 'Tarjeta NFC',
        })"
        @panorama-preview.window="onPanoramaPreview($event.detail)"
    >
        <div class="space-y-5 xl:col-span-5">
            <x-ui.card>
                <form
                    method="POST"
                    action="{{ route('admin.nfc.tour.update', $point) }}"
                    enctype="multipart/form-data"
                    class="space-y-4"
                    novalidate
                    @dropzone-error.window="$dispatch('open-modal', 'panorama-error')"
                >
                    @csrf
                    @method('PATCH')

                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-primary">Panorama 360°</p>
                        <p class="mt-1 text-sm text-secondary-light">
                            Sube la imagen, gira la vista y haz un clic corto donde esté la tarjeta NFC. Verás la moneda FESC/NFC en el preview; al guardar quedará en la vista pública.
                        </p>
                    </div>

                    <div>
                        <x-form.label for="panorama">Imagen panorámica</x-form.label>
                        <div class="mt-2">
                            <x-form.dropzone
                                name="panorama"
                                id="panorama"
                                payload-name="panorama_data"
                                accept="image/jpeg,image/png,image/webp"
                                :max-size-mb="12"
                                title="Haz clic para subir"
                                subtitle="o arrastra y suelta el panorama aquí"
                                hint="JPG, PNG o WEBP. Máximo 12 MB. Formato equirectangular 360°."
                                preview-event="panorama-preview"
                            />
                        </div>
                        @if ($point->panorama_path)
                            <p class="mt-2 text-xs text-secondary-light">
                                Panorama actual: <span class="font-medium text-secondary">{{ basename($point->panorama_path) }}</span>
                            </p>
                        @endif
                    </div>

                    <div>
                        <x-form.label for="tour_description">Texto de ayuda (opcional)</x-form.label>
                        <textarea
                            id="tour_description"
                            name="tour_description"
                            rows="3"
                            class="form-control"
                            data-auto-grow
                            placeholder="Ej. Gira la vista hasta encontrar el punto rojo: ahí está la tarjeta NFC."
                        >{{ old('tour_description', $point->tour_description) }}</textarea>
                    </div>

                    <div class="rounded-2xl border border-secondary/10 bg-white/40 p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-primary">Marcador NFC</p>
                        <p class="mt-1 text-sm text-secondary-light">
                            En el preview: arrastra para mirar y haz un clic corto donde esté el chip. Ahí se fijará la moneda NFC.
                        </p>

                        <input type="hidden" name="nfc_marker_theta" :value="draft.theta">
                        <input type="hidden" name="nfc_marker_phi" :value="draft.phi">

                        <div class="admin-nfc-marker-preview mt-4">
                            <div class="admin-nfc-marker-preview__coin" aria-hidden="true">
                                <x-nfc.coin class="admin-nfc-marker-preview__coin-body" front="FESC" back="NFC" caption="NFC" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-secondary">
                                    <span x-show="draft.theta !== '' && draft.phi !== ''">
                                        Moneda colocada en el panorama
                                    </span>
                                    <span x-show="draft.theta === '' || draft.phi === ''" x-cloak>
                                        Sin marcar todavía
                                    </span>
                                </p>
                                <p class="mt-1 text-xs text-secondary-light">
                                    Misma moneda FESC/NFC de la landing: así se verá el punto en la vista 360°.
                                </p>

                                <button
                                    type="button"
                                    class="mt-2 text-sm font-semibold text-danger hover:underline"
                                    x-show="draft.theta !== '' && draft.phi !== ''"
                                    x-cloak
                                    @click="clearMarker()"
                                >
                                    Quitar marcador
                                </button>
                            </div>
                        </div>
                    </div>

                    <label class="flex items-center gap-3 text-sm font-medium text-secondary">
                        <input type="checkbox" name="tour_enabled" value="1" @checked(old('tour_enabled', $point->tour_enabled)) class="rounded border-secondary/30 text-primary focus:ring-primary">
                        Activar vista 360° pública
                    </label>

                    <x-ui.button type="submit" class="!rounded-2xl">Guardar vista 360°</x-ui.button>
                </form>

                <x-modal name="panorama-error" maxWidth="sm" focusable>
                    <div
                        class="p-6"
                        x-data="{ message: 'No fue posible cargar la imagen.' }"
                        @dropzone-error.window="message = $event.detail.message"
                    >
                        <h2 class="text-lg font-bold tracking-tight text-secondary">Imagen no válida</h2>
                        <p class="mt-2 text-sm text-secondary-light" x-text="message"></p>
                        <div class="mt-6 flex justify-end">
                            <x-ui.button type="button" variant="secondary" x-on:click="$dispatch('close')">
                                Entendido
                            </x-ui.button>
                        </div>
                    </div>
                </x-modal>
            </x-ui.card>
        </div>

        <div class="xl:col-span-7">
            <x-ui.card class="overflow-hidden !p-0">
                <div class="admin-tour-preview">
                    <div class="admin-tour-preview__canvas" x-ref="canvas"></div>

                    <div
                        class="admin-tour-preview__empty"
                        x-show="!hasPreviewImage"
                        x-cloak
                    >
                        <x-ui.empty-state
                            embedded
                            title="Sin panorama"
                            description="Sube una imagen 360° a la izquierda. Luego haz clic aquí para marcar la tarjeta NFC con el punto rojo."
                        />
                    </div>

                    <p class="admin-tour-preview__help" x-show="hasPreviewImage" x-cloak>
                        Arrastra para mirar · Clic corto para fijar la moneda NFC
                        <span x-show="draft.theta !== '' && draft.phi !== ''">
                            · marcador listo
                        </span>
                    </p>
                </div>
            </x-ui.card>
        </div>
    </div>
@endsection
