@props([
    'name',
])

<span {{ $attributes->merge(['class' => 'inline-flex h-12 w-12 items-center justify-center rounded-full border border-white/45 bg-white/35 text-primary shadow-[0_4px_14px_rgb(15_23_42_/_0.06)] backdrop-blur-md']) }}>
    <x-icon :name="$name" class="h-5 w-5" />
</span>
