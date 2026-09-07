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

        $toasts[] = [
            'type' => 'danger',
            'title' => $errorBagTitles[$bagName] ?? 'No fue posible continuar',
            'messages' => array_values(array_unique($bag->all())),
            'timeout' => $timeoutFor('danger'),
        ];
    }
@endphp

@if ($toasts !== [])
    <div x-data>
        <template x-teleport="body">
            <div
                class="toast-viewport"
                data-alert-host
                aria-live="polite"
                aria-relevant="additions"
            >
                @foreach ($toasts as $toast)
                    <x-ui.alert
                        class="pointer-events-auto"
                        :toast="true"
                        :type="$toast['type']"
                        :title="$toast['title']"
                        :timeout="$toast['timeout']"
                        dismissible
                    >
                        @if (count($toast['messages']) === 1)
                            {{ $toast['messages'][0] }}
                        @else
                            <ul class="list-disc space-y-0.5 ps-4">
                                @foreach ($toast['messages'] as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </x-ui.alert>
                @endforeach
            </div>
        </template>
    </div>
@endif
