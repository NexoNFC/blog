@props([
    'steps' => [],
    'label' => 'Recorrido de la información',
    'onDark' => false,
])

<ol {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2']) }} aria-label="{{ $label }}">
    @foreach ($steps as $step)
        <li @class([
            'inline-flex items-center gap-2 px-3 py-1.5',
            'glass-chip' => ! $onDark,
            'rounded-full border border-white/20 bg-white/10 backdrop-blur-md' => $onDark,
        ])>
            <x-icon :name="$step['icon']" @class(['h-4 w-4', 'text-secondary' => ! $onDark, 'text-white' => $onDark]) />
            <span @class(['text-xs font-medium tracking-wide', 'text-secondary' => ! $onDark, 'text-white/90' => $onDark])>
                {{ $step['label'] }}
            </span>
        </li>
    @endforeach
</ol>
