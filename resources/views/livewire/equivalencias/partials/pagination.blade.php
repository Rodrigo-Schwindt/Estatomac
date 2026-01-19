@if ($equivalencias->hasPages())
    <nav class="flex items-center justify-between border-t border-slate-200 px-4 py-3 sm:px-6">
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($equivalencias->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-400 bg-slate-50 border border-slate-200 cursor-default rounded-md">
                    Anterior
                </span>
            @else
                <a href="{{ $equivalencias->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50">
                    Anterior
                </a>
            @endif

            @if ($equivalencias->hasMorePages())
                <a href="{{ $equivalencias->nextPageUrl() }}" class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50">
                    Siguiente
                </a>
            @else
                <span class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-slate-400 bg-slate-50 border border-slate-200 cursor-default rounded-md">
                    Siguiente
                </span>
            @endif
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-center">
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    @if ($equivalencias->onFirstPage())
                        <span class="relative inline-flex items-center rounded-l-md px-2 py-2 text-slate-400 bg-slate-50 cursor-default">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $equivalencias->previousPageUrl() }}" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-slate-400 hover:bg-slate-50">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    @foreach ($equivalencias->links()->elements[0] as $page => $url)
                        @if ($page == $equivalencias->currentPage())
                            <span class="relative z-10 inline-flex items-center bg-blue-600 px-4 py-2 text-sm font-semibold text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-50">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if ($equivalencias->hasMorePages())
                        <a href="{{ $equivalencias->nextPageUrl() }}" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-slate-400 hover:bg-slate-50">
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