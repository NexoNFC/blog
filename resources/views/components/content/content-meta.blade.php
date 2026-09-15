@props([
    'content',
    'onDark' => false,
    'showType' => true,
])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2']) }}>
    @if ($showType && ! empty($content['type']))
        <x-ui.badge>{{ str_replace('-', ' ', $content['type']) }}</x-ui.badge>
    @endif

    @if (! empty($content['published_at']))
        <time @class(['text-xs', 'text-white/85' => $onDark, 'text-secondary-light' => ! $onDark]) datetime="{{ $content['published_at'] }}">
            {{ $content['published_at'] }}
        </time>
    @endif
</div>
