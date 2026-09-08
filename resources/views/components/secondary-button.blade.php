<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-xl border border-white/60 bg-white/55 px-4 py-2.5 text-sm font-semibold text-secondary shadow-sm backdrop-blur-md transition hover:bg-white/80 focus:outline-none focus:ring-4 focus:ring-primary/20 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
