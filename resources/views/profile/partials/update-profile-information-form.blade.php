<section>
    <header>
        <h2 class="text-lg font-bold tracking-tight text-secondary">Información del perfil</h2>
        <p class="mt-1 text-sm text-secondary-light">Actualiza tu foto, descripción, nombre y correo electrónico.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="mt-6 space-y-5"
        novalidate
        x-data="{
            preview: @js($user->avatarUrl()),
            pendingPreview: null,
            removeAvatar: false,
            openPreview(detail) {
                if (this.pendingPreview) {
                    URL.revokeObjectURL(this.pendingPreview);
                }

                this.pendingPreview = detail.url;
                this.removeAvatar = false;
                this.$dispatch('open-modal', 'avatar-preview');
                this.$nextTick(() => {
                    this.$dispatch('avatar-editor-load', detail);
                });
            },
            confirmPreview(detail) {
                if (this.pendingPreview && this.pendingPreview !== detail?.url) {
                    URL.revokeObjectURL(this.pendingPreview);
                }

                this.preview = detail.url;
                this.pendingPreview = null;
                this.removeAvatar = false;

                if (detail.file) {
                    this.$dispatch('dropzone-replace', { file: detail.file });
                }
            },
            cancelPreview() {
                if (this.pendingPreview) {
                    URL.revokeObjectURL(this.pendingPreview);
                }

                this.pendingPreview = null;
                this.$dispatch('dropzone-clear');
            },
            clearAvatar() {
                if (this.pendingPreview) {
                    URL.revokeObjectURL(this.pendingPreview);
                }

                this.preview = null;
                this.pendingPreview = null;
                this.removeAvatar = true;
                this.$dispatch('dropzone-clear');
            },
        }"
        @dropzone-preview.window="openPreview($event.detail)"
        @avatar-preview-confirm.window="confirmPreview($event.detail)"
        @avatar-preview-cancel.window="cancelPreview()"
        @close-modal.window="if ($event.detail === 'avatar-preview' && pendingPreview) cancelPreview()"
    >
        @csrf
        @method('patch')

        <div>
            <x-form.label for="avatar">Foto de perfil</x-form.label>

            <div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-start">
                <div class="shrink-0 self-center sm:self-start">
                    <img
                        x-show="preview"
                        x-cloak
                        :src="preview"
                        alt="Vista previa de la foto de perfil"
                        class="admin-avatar h-20 w-20 object-cover"
                    >
                    <span
                        x-show="!preview"
                        class="admin-avatar inline-flex h-20 w-20 text-2xl"
                    >{{ $user->initial() }}</span>
                </div>

                <div class="min-w-0 flex-1 space-y-3">
                    <x-form.dropzone
                        name="avatar"
                        id="avatar"
                        accept="image/jpeg,image/png,image/webp"
                        :max-size-mb="2"
                        title="Haz clic para subir"
                        subtitle="o arrastra y suelta tu foto aquí"
                        hint="JPG, PNG o WEBP. Máximo 2 MB."
                        preview-event="dropzone-preview"
                        x-on:dropzone-clear.window="clear()"
                        x-on:dropzone-replace.window="replaceFile($event.detail.file)"
                    />

                    <input type="hidden" name="remove_avatar" :value="removeAvatar ? '1' : '0'">

                    @if ($user->avatarUrl())
                        <button
                            type="button"
                            class="text-sm font-bold text-primary underline-offset-2 hover:underline"
                            @click="clearAvatar()"
                            x-show="preview || !removeAvatar"
                        >
                            Quitar foto actual
                        </button>
                    @endif
                </div>
            </div>

            <x-form.errors name="avatar" />
        </div>

        <div>
            <x-form.label for="name" required>Nombre</x-form.label>
            <x-form.input id="name" name="name" :value="old('name', $user->name)" required autocomplete="name" />
            <x-form.errors name="name" />
        </div>

        <div>
            <x-form.label for="email" required>Correo electrónico</x-form.label>
            <x-form.input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-form.errors name="email" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="mt-2 text-sm text-secondary-light">
                    Tu correo no está verificado.
                    <button form="send-verification" class="font-semibold text-primary underline-offset-2 hover:underline">
                        Reenviar enlace de verificación
                    </button>
                </p>
            @endif
        </div>

        <div>
            <x-form.label for="bio">Descripción</x-form.label>
            <textarea
                id="bio"
                name="bio"
                rows="4"
                maxlength="280"
                class="form-control"
                data-auto-grow
                placeholder="Breve presentación para tu cuenta de administrador FESC."
            >{{ old('bio', $user->bio) }}</textarea>
            <p class="mt-1 text-xs text-secondary-light">Hasta 280 caracteres.</p>
            <x-form.errors name="bio" />
        </div>

        <x-ui.button type="submit" class="!rounded-2xl">Guardar</x-ui.button>

        <x-modal name="avatar-preview" maxWidth="lg" focusable>
            <div class="p-6">
                <h2 class="text-lg font-bold tracking-tight text-secondary">Acomodar foto de perfil</h2>
                <p class="mt-1 text-sm text-secondary-light">
                    Arrastra, gira y ajusta el zoom para encuadrar tu foto.
                </p>

                <div class="mt-5">
                    <x-form.avatar-editor />
                </div>
            </div>
        </x-modal>

        <x-modal name="avatar-error" maxWidth="sm" focusable>
            <div
                class="p-6"
                x-data="{ message: 'No fue posible cargar la imagen.' }"
                @dropzone-error.window="message = $event.detail.message"
            >
                <h2 class="text-lg font-bold tracking-tight text-secondary">Imagen no válida</h2>
                <p class="mt-2 text-sm text-secondary-light" x-text="message"></p>
                <div class="mt-6 flex justify-end">
                    <x-ui.button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'avatar-error')">
                        Entendido
                    </x-ui.button>
                </div>
            </div>
        </x-modal>
    </form>
</section>
