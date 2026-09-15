@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación" class="admin-pagination__nav">
        <p class="admin-pagination__summary">
            Mostrando
            <span>{{ $paginator->firstItem() }}</span>
            –
            <span>{{ $paginator->lastItem() }}</span>
            de
            <span>{{ $paginator->total() }}</span>
        </p>

        <div class="admin-pagination__controls">
            @if ($paginator->onFirstPage())
                <span class="admin-pagination__btn is-disabled" aria-disabled="true">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="admin-pagination__btn" rel="prev">Anterior</a>
            @endif

            <ul class="admin-pagination__pages">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li><span class="admin-pagination__ellipsis">{{ $element }}</span></li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <li>
                                @if ($page == $paginator->currentPage())
                                    <span class="admin-pagination__page is-active" aria-current="page">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="admin-pagination__page">{{ $page }}</a>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach
            </ul>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="admin-pagination__btn" rel="next">Siguiente</a>
            @else
                <span class="admin-pagination__btn is-disabled" aria-disabled="true">Siguiente</span>
            @endif
        </div>
    </nav>
@endif
