@props([
    'text',
    'class' => 'space-y-4 text-base leading-relaxed text-secondary',
])

@php
    $html = app(\App\Support\ContentBodyFormatter::class)->toHtml((string) $text);
@endphp

@if ($html !== '')
    <div {{ $attributes->class([$class]) }}>
        {!! $html !!}
    </div>
@endif
