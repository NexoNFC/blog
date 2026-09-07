@props([
    'type' => 'info',
    'title' => null,
    'message' => null,
    'dismissible' => false,
    'timeout' => null,
    'toast' => false,
])

@php
    $type = $type === 'error' ? 'danger' : $type;
    $toast = (bool) $toast;

    $styles = $toast
        ? 'border-white/70 bg-white/95 text-secondary shadow-lg'
        : match ($type) {
            'success' => 'border-success/25 bg-success-soft/80 text-success',
            'danger' => 'border-danger/25 bg-danger-soft/80 text-danger',
            'warning' => 'border-warning/25 bg-warning-soft/80 text-warning',
            'neutral' => 'border-white/50 bg-white/70 text-secondary',
            default => 'border-info/20 bg-info-soft/80 text-info',
        };

    $iconWrap = match ($type) {
        'success' => 'bg-success-soft text-success',
        'danger' => 'bg-danger-soft text-danger',
        'warning' => 'bg-warning-soft text-warning',
        default => 'bg-info-soft text-info',
    };

    $role = in_array($type, ['danger', 'warning'], true) ? 'alert' : 'status';
    $body = $message ?? ($slot->isNotEmpty() ? $slot : null);
@endphp

<div
    @if ($dismissible || $timeout)
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-4 opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-4 opacity-0"
        @if ($timeout) x-init="setTimeout(() => show = false, {{ (int) $timeout }})" @endif
    @endif
    {{ $attributes->merge(['class' => "flex gap-3 rounded-xl border px-4 py-3 text-sm shadow-sm backdrop-blur-md {$styles}", 'role' => $role]) }}
>
    <span @class([
        'shrink-0',
        'mt-0.5' => ! $toast,
        'inline-flex h-8 w-8 items-center justify-center rounded-lg '.$iconWrap => $toast,
    ])>
        @if ($type === 'success')
            <x-icon name="badge-check" class="h-5 w-5" />
        @elseif ($type === 'danger')
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
            </svg>
        @elseif ($type === 'warning')
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
            </svg>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
            </svg>
        @endif
    </span>
    <div class="min-w-0 flex-1">
        @if ($title)
            <p @class(['font-semibold', 'text-secondary' => $toast])>{{ $title }}</p>
        @endif
        @if ($body)
            <div @class(['mt-0.5', 'text-secondary-light' => $toast && $title])>{{ $body }}</div>
        @endif
    </div>
    @if ($dismissible)
        <button type="button" class="shrink-0 rounded-lg p-1 text-secondary-light transition hover:bg-muted hover:text-secondary" @click="show = false" aria-label="Cerrar aviso">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14" class="h-3.5 w-3.5" aria-hidden="true">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    @endif
</div>
