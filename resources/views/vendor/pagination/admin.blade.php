@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $windowStart = max(1, $currentPage - 2);
        $windowEnd = min($lastPage, $currentPage + 2);
    @endphp

    <nav class="adm-pagination" role="navigation" aria-label="Pagination">
        <div class="adm-pagination-meta">
            <span>Page {{ $currentPage }} / {{ $lastPage }}</span>
            <span>{{ $paginator->total() }} element(s)</span>
        </div>

        <div class="adm-pagination-links">
            @if ($paginator->onFirstPage())
                <span class="adm-pagination-link is-disabled">Precedent</span>
            @else
                <a class="adm-pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Precedent</a>
            @endif

            @if ($windowStart > 1)
                <a class="adm-pagination-link" href="{{ $paginator->url(1) }}">1</a>
                @if ($windowStart > 2)
                    <span class="adm-pagination-link is-ellipsis">...</span>
                @endif
            @endif

            @for ($page = $windowStart; $page <= $windowEnd; $page++)
                @if ($page === $currentPage)
                    <span class="adm-pagination-link is-current">{{ $page }}</span>
                @else
                    <a class="adm-pagination-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                @endif
            @endfor

            @if ($windowEnd < $lastPage)
                @if ($windowEnd < $lastPage - 1)
                    <span class="adm-pagination-link is-ellipsis">...</span>
                @endif
                <a class="adm-pagination-link" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
            @endif

            @if ($paginator->hasMorePages())
                <a class="adm-pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Suivant</a>
            @else
                <span class="adm-pagination-link is-disabled">Suivant</span>
            @endif
        </div>
    </nav>
@endif
