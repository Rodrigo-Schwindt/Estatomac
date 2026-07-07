@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación" class="todotex-pagination">
        <div class="todotex-pagination-inner">

            {{-- Botón anterior --}}
            @if ($paginator->onFirstPage())
                <span class="todotex-page-btn todotex-page-btn--disabled" aria-disabled="true">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" wire:navigate rel="prev" class="todotex-page-btn todotex-page-btn--nav" aria-label="Anterior">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            @endif

            {{-- Números de página --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="todotex-page-dots">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="todotex-page-btn todotex-page-btn--active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" wire:navigate class="todotex-page-btn todotex-page-btn--number">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Botón siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" wire:navigate rel="next" class="todotex-page-btn todotex-page-btn--nav" aria-label="Siguiente">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span class="todotex-page-btn todotex-page-btn--disabled" aria-disabled="true">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif

        </div>

        <p class="todotex-pagination-info">
            Mostrando
            <span class="font-semibold">{{ $paginator->firstItem() }}</span>–<span class="font-semibold">{{ $paginator->lastItem() }}</span>
            de
            <span class="font-semibold">{{ $paginator->total() }}</span>
            productos
        </p>
    </nav>

    <style>
        .todotex-pagination {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            padding-top: 32px;
        }

        .todotex-pagination-inner {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .todotex-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 4px;
            border-radius: 8px;
            font-family: Inter, sans-serif;
            font-size: 14px;
            font-weight: 500;
            line-height: 1;
            text-decoration: none;
            transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }

        .todotex-page-btn--nav {
            color: #23378C;
            background: #fff;
            border: 1.5px solid #dde3ea;
        }

        .todotex-page-btn--nav:hover {
            background: #23378C;
            border-color: #23378C;
            color: #fff;
        }

        .todotex-page-btn--number {
            color: #444;
            background: #fff;
            border: 1.5px solid #dde3ea;
        }

        .todotex-page-btn--number:hover {
            background: #f0f3fc;
            border-color: #23378C;
            color: #23378C;
        }

        .todotex-page-btn--active {
            background: #23378C;
            border: 1.5px solid #23378C;
            color: #fff;
            font-weight: 700;
            pointer-events: none;
        }

        .todotex-page-btn--disabled {
            color: #c0c8d4;
            background: #f5f6f8;
            border: 1.5px solid #e8eaed;
            pointer-events: none;
        }

        .todotex-page-dots {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 40px;
            color: #8b9ab0;
            font-family: Inter, sans-serif;
            font-size: 14px;
            letter-spacing: 0.05em;
        }

        .todotex-pagination-info {
            font-family: Inter, sans-serif;
            font-size: 13px;
            color: #8b9ab0;
        }

        @media (max-width: 639px) {
            .todotex-page-btn {
                min-width: 36px;
                height: 36px;
                font-size: 13px;
                border-radius: 7px;
            }

            .todotex-pagination-inner {
                gap: 4px;
            }
        }
    </style>
@endif
