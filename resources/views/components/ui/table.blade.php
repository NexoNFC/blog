<div {{ $attributes->merge(['class' => 'glass-panel overflow-hidden rounded-2xl']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            {{ $slot }}
        </table>
    </div>
</div>
