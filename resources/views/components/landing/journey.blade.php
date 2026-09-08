@props([
    'steps' => [],
    'label' => 'Recorrido de la información',
])

<ol {{ $attributes->merge(['class' => 'flex flex-wrap items-center justify-center gap-2']) }} aria-label="{{ $label }}">
    @foreach ($steps as $step)
        <li class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 backdrop-blur-md">
            <x-icon :name="$step['icon']" class="h-4 w-4 text-white" />
            <span class="text-xs font-medium tracking-wide text-white/90">{{ $step['label'] }}</span>
        </li>
    @endforeach
</ol>
