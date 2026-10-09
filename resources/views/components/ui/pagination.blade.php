@props([
    'paginator' => null,
    'ajax' => false,
])

@if ($ajax)
    <div
        {{ $attributes->class(['admin-pagination mt-6']) }}
        x-show="Number(meta?.last_page || 0) > 1"
        x-cloak
    >
        <nav role="navigation" aria-label="Paginación" class="admin-pagination__nav">
            <p class="admin-pagination__summary">
                Mostrando
                <span x-text="meta.from ?? 0"></span>
                –
                <span x-text="meta.to ?? 0"></span>
                de
                <span x-text="meta.total ?? 0"></span>
            </p>

            <div class="admin-pagination__controls">
                <button
                    type="button"
                    class="admin-pagination__btn"
                    :class="{ 'is-disabled': Number(meta.current_page || 1) <= 1 }"
                    :disabled="loading || Number(meta.current_page || 1) <= 1"
                    x-on:click="goToPage(Number(meta.current_page || 1) - 1)"
                >
                    Anterior
                </button>

                <ul class="admin-pagination__pages">
                    <template x-for="(item, index) in pageItems" :key="'page-' + index + '-' + item">
                        <li>
                            <span class="admin-pagination__ellipsis" x-show="item === '...'">…</span>
                            <button
                                type="button"
                                class="admin-pagination__page"
                                x-show="item !== '...'"
                                :class="{ 'is-active': Number(item) === Number(meta.current_page || 1) }"
                                :disabled="loading || Number(item) === Number(meta.current_page || 1)"
                                :aria-current="Number(item) === Number(meta.current_page || 1) ? 'page' : null"
                                x-on:click="goToPage(item)"
                                x-text="item"
                            ></button>
                        </li>
                    </template>
                </ul>

                <button
                    type="button"
                    class="admin-pagination__btn"
                    :class="{ 'is-disabled': Number(meta.current_page || 1) >= Number(meta.last_page || 1) }"
                    :disabled="loading || Number(meta.current_page || 1) >= Number(meta.last_page || 1)"
                    x-on:click="goToPage(Number(meta.current_page || 1) + 1)"
                >
                    Siguiente
                </button>
            </div>
        </nav>
    </div>
@elseif ($paginator && $paginator->hasPages())
    <div {{ $attributes->class(['admin-pagination mt-6']) }}>
        {{ $paginator->onEachSide(1)->links('pagination.admin') }}
    </div>
@endif
