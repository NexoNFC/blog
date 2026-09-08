@props([
    'name',
])

<span {{ $attributes->merge(['class' => 'inline-flex h-12 w-12 items-center justify-center rounded-full border border-white/50 bg-white/70 text-primary shadow-sm backdrop-blur-md']) }}>
    <x-icon :name="$name" class="h-5 w-5" />
</span>
