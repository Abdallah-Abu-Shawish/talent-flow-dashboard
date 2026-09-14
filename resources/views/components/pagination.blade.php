@props(['paginator'])
@if($paginator)
    <nav class="pagination" aria-label="Pagination">
        <p>@if($paginator->total())Showing <strong>{{ number_format($paginator->firstItem() ?? 0) }}–{{ number_format($paginator->lastItem() ?? 0) }}</strong> of <strong>{{ number_format($paginator->total()) }}</strong> records @else 0 records @endif</p>
        @if($paginator->hasPages())
            <div class="pagination-links">
                @if($paginator->onFirstPage())<span class="pagination-button disabled" aria-disabled="true"><x-icon name="back" /><span class="sr-only">Previous page</span></span>@else<a class="pagination-button" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page"><x-icon name="back" /></a>@endif
                @foreach($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                    <a @class(['pagination-button', 'active' => $page === $paginator->currentPage()]) href="{{ $url }}" aria-label="Page {{ $page }}" @if($page === $paginator->currentPage()) aria-current="page" @endif>{{ $page }}</a>
                @endforeach
                @if($paginator->hasMorePages())<a class="pagination-button" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page"><x-icon name="arrow" /></a>@else<span class="pagination-button disabled" aria-disabled="true"><x-icon name="arrow" /><span class="sr-only">Next page</span></span>@endif
            </div>
        @endif
    </nav>
@endif
