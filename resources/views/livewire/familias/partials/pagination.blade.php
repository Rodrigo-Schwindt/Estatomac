@if($familias->hasPages())
    <nav class="flex items-center justify-between border-t border-slate-200 px-4 py-4">
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-center">
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    @if ($familias->onFirstPage())
                        <span class="relative inline-flex items-center rounded-l-md px-2 py-2 text-slate-400 bg-slate-50 cursor-default">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $familias->previousPageUrl() }}" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-slate-400 hover:bg-slate-50">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    @php
                        $currentPage = $familias->currentPage();
                        $lastPage = $familias->lastPage();
                        $start = max(1, $currentPage - 2);
                        $end = min($lastPage, $currentPage + 2);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $familias->url(1) }}" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-50">1</a>
                        @if($start > 2)
                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-700">...</span>
                        @endif
                    @endif

                    @for($i = $start; $i <= $end; $i++)
                        @if ($i == $currentPage)
                            <span class="relative z-10 inline-flex items-center bg-blue-600 px-4 py-2 text-sm font-semibold text-white">{{ $i }}</span>
                        @else
                            <a href="{{ $familias->url($i) }}" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-50">{{ $i }}</a>
                        @endif
                    @endfor

                    @if($end < $lastPage)
                        @if($end < $lastPage - 1)
                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-700">...</span>
                        @endif
                        <a href="{{ $familias->url($lastPage) }}" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-50">{{ $lastPage }}</a>
                    @endif

                    @if ($familias->hasMorePages())
                        <a href="{{ $familias->nextPageUrl() }}" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-slate-400 hover:bg-slate-50">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span class="relative inline-flex items-center rounded-r-md px-2 py-2 text-slate-400 bg-slate-50 cursor-default">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif
                </nav>
            </div>
        </div>
    </nav>
@endif
