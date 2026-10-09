@php
    $toasts = [];

    $timeoutFor = static fn (string $type): int => in_array($type, ['danger', 'warning'], true) ? 8000 : 5000;

    if (session()->has('status')) {
        $status = (string) session('status');

        $statusMessage = match ($status) {
            'profile-updated' => 'Los datos del perfil se actualizaron correctamente.',
            'password-updated' => 'La contraseña se actualizó correctamente.',
            'verification-link-sent' => 'Se envió un nuevo enlace de verificación.',
            default => $status,
        };

        $toasts[] = [
            'type' => 'success',
            'title' => 'Listo',
            'messages' => [$statusMessage],
            'timeout' => $timeoutFor('success'),
        ];
    }

    if (session()->has('alert')) {
        $type = (string) session('alert.type', 'info');

        $toasts[] = [
            'type' => $type,
            'title' => session('alert.title'),
            'messages' => array_filter([(string) session('alert.message')]),
            'timeout' => $timeoutFor($type === 'error' ? 'danger' : $type),
        ];
    }

    $errorBagTitles = [
        'updatePassword' => 'No fue posible actualizar la contraseña',
        'userDeletion' => 'No fue posible eliminar la cuenta',
    ];

    $viewErrors = $errors ?? new \Illuminate\Support\ViewErrorBag;

    foreach ($viewErrors->getBags() as $bagName => $bag) {
        if ($bag->isEmpty()) {
            continue;
        }

        if ($bagName === 'default' && $bag->has('credentials')) {
            $toasts[] = [
                'type' => 'danger',
                'title' => 'No fue posible iniciar sesión',
                'messages' => [(string) $bag->first('credentials')],
                'timeout' => $timeoutFor('danger'),
            ];

            continue;
        }

        $toastTitle = $errorBagTitles[$bagName] ?? 'No fue posible continuar';

        if ($bagName === 'default' && $bag->has('panorama')) {
            $toastTitle = 'No se pudo guardar el recorrido';
        }

        $toasts[] = [
            'type' => 'danger',
            'title' => $toastTitle,
            'messages' => array_values(array_unique($bag->all())),
            'timeout' => $timeoutFor('danger'),
        ];
    }
@endphp

<div
    x-data="toastHost({{ \Illuminate\Support\Js::from(array_values($toasts)) }})"
    x-on:toast.window="push($event.detail)"
>
    <template x-teleport="body">
        <div
            class="toast-viewport"
            data-alert-host
            aria-live="polite"
            aria-relevant="additions"
        >
            <template x-for="toast in toasts" :key="toast.id">
                <div
                    x-show="toast.show"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="translate-x-4 opacity-0"
                    x-transition:enter-end="translate-x-0 opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="translate-x-0 opacity-100"
                    x-transition:leave-end="translate-x-4 opacity-0"
                    class="pointer-events-auto flex gap-3 rounded-xl border border-white/70 bg-white/95 px-4 py-3 text-sm text-secondary shadow-lg backdrop-blur-md"
                    :role="['danger', 'warning'].includes(toast.type) ? 'alert' : 'status'"
                >
                    <span
                        class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
                        :class="iconClass(toast.type)"
                        aria-hidden="true"
                    >
                        <template x-if="toast.type === 'success'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
                        </template>
                        <template x-if="toast.type === 'danger'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                        </template>
                        <template x-if="toast.type === 'warning'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        </template>
                        <template x-if="!['success','danger','warning'].includes(toast.type)">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
                        </template>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-secondary" x-text="toast.title" x-show="toast.title"></p>
                        <div class="mt-0.5 text-secondary-light" x-show="toast.messages.length === 1" x-text="toast.messages[0]"></div>
                        <ul class="mt-0.5 list-disc space-y-0.5 ps-4 text-secondary-light" x-show="toast.messages.length > 1">
                            <template x-for="message in toast.messages" :key="message">
                                <li x-text="message"></li>
                            </template>
                        </ul>
                    </div>
                    <button
                        type="button"
                        class="shrink-0 rounded-lg p-1 text-secondary-light transition hover:bg-muted hover:text-secondary"
                        x-on:click="dismiss(toast.id)"
                        aria-label="Cerrar aviso"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14" class="h-3.5 w-3.5" aria-hidden="true">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>
    </template>
</div>
