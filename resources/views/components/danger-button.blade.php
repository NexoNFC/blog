<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-xl border border-transparent bg-danger px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-danger/90 focus:outline-none focus:ring-4 focus:ring-danger/30 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
