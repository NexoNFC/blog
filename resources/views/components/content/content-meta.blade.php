@props([
    'content',
    'onDark' => false,
])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2']) }}>
    <x-ui.badge>{{ $content['type'] }}</x-ui.badge>

    @if (($content['type'] ?? null) === 'externo' || ! empty($content['external_url']))
        <x-ui.badge tone="warning">Externo</x-ui.badge>
    @endif

    @if (! empty($content['published_at']))
        <time @class(['text-xs', 'text-white/85' => $onDark, 'text-secondary-light' => ! $onDark]) datetime="{{ $content['published_at'] }}">
            {{ $content['published_at'] }}
        </time>
    @endif
</div>
