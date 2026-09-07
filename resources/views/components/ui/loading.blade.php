@props([
    'label' => 'Cargando',
])

<div {{ $attributes->merge(['class' => 'flex items-center justify-center gap-3 py-8 text-sm text-secondary-light']) }} role="status" aria-live="polite">
    <svg class="h-5 w-5 animate-spin text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"></path>
    </svg>
    <span>{{ $label }}</span>
</div>
