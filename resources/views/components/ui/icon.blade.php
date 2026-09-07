@props([
    'name',
])

@php
    $icons = [
        'nfc' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 8.25a5.25 5.25 0 0 1 7.5 0M5.25 5.25a9.75 9.75 0 0 1 13.5 0M12 12.75v.008"/><circle cx="12" cy="15.75" r="1.25" fill="currentColor" stroke="none"/>',
        'phone' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5h3a1.5 1.5 0 0 1 1.5 1.5v18a1.5 1.5 0 0 1-1.5 1.5h-3a1.5 1.5 0 0 1-1.5-1.5V3a1.5 1.5 0 0 1 1.5-1.5Z"/><path stroke-linecap="round" d="M11 19.5h2"/>',
        'spark' => '<path stroke-linecap="round" stroke-linejoin="round" d="m12 3 1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z"/>',
        'compass' => '<circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="m14.5 9.5-1.2 4.3-4.3 1.2 1.2-4.3 4.3-1.2Z"/>',
        'building' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.25V6.75A1.5 1.5 0 0 1 6 5.25h12a1.5 1.5 0 0 1 1.5 1.5v13.5"/><path stroke-linecap="round" d="M9 20.25V12h6v8.25M4.5 20.25h15"/><path stroke-linecap="round" d="M9 8.25h.01M12 8.25h.01M15 8.25h.01M9 11.25h.01M12 11.25h.01M15 11.25h.01"/>',
        'book' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25A2.25 2.25 0 0 1 6.75 3h12v16.5H6.75A2.25 2.25 0 0 0 4.5 21.75V5.25Z"/><path stroke-linecap="round" d="M4.5 5.25A2.25 2.25 0 0 1 6.75 3"/>',
        'door' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5.25 20.25V4.5A1.5 1.5 0 0 1 6.75 3h10.5a1.5 1.5 0 0 1 1.5 1.5v15.75"/><path stroke-linecap="round" d="M9.75 12h.01"/>',
        'stage' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 19.5h18M5.25 19.5V10.5l6.75-4.5 6.75 4.5v9"/><path stroke-linecap="round" d="M9.75 19.5v-4.5h4.5v4.5"/>',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex h-12 w-12 items-center justify-center rounded-full bg-primary-soft text-primary']) }}>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-6 w-6" aria-hidden="true">
        {!! $icons[$name] ?? $icons['spark'] !!}
    </svg>
</span>
