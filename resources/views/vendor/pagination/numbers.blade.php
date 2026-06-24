@if ($paginator->hasPages())
<style>
    .pag-nav {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        margin: 2rem 0;
        width: 100%;
    }
    .pag-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 10px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.18s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        line-height: 1;
    }
    .pag-btn:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        color: #1e293b;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .pag-btn.active {
        background: #3b82f6;
        border-color: #3b82f6;
        color: #ffffff;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
        cursor: default;
    }
    .pag-btn.disabled {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #cbd5e1;
        cursor: not-allowed;
        box-shadow: none;
    }
    .pag-dots {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        color: #94a3b8;
        font-size: 0.875rem;
        font-weight: 500;
    }
</style>

<nav role="navigation" aria-label="Pagination Navigation">
    <div class="pag-nav">

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="pag-btn disabled" aria-disabled="true">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pag-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pag-dots">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pag-btn active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pag-btn" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pag-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="pag-btn disabled" aria-disabled="true">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif

    </div>
</nav>
@endif

