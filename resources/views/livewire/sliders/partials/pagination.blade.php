@if($sliders->hasPages())
    <div class="flex items-center justify-between pt-5 border-t border-slate-200">
        <button {{ !$sliders->onFirstPage() ? '' : 'disabled' }}
                class="px-4 py-2 border border-slate-300 rounded-md text-slate-600 text-sm hover:bg-slate-50 transition disabled:opacity-50">
            ← Anterior
        </button>

        <span class="text-sm text-slate-500">
            {{ $sliders->firstItem() }}–{{ $sliders->lastItem() }} de {{ $sliders->total() }}
        </span>

        <button {{ $sliders->hasMorePages() ? '' : 'disabled' }}
                class="px-4 py-2 border border-slate-300 rounded-md text-slate-600 text-sm hover:bg-slate-50 transition disabled:opacity-50">
            Siguiente →
        </button>
    </div>

    <div class="flex justify-center gap-1 mt-4 flex-wrap">
        @foreach ($sliders->getUrlRange(1, $sliders->lastPage()) as $page => $url)
            <a href="{{ $url }}" 
               class="pagination-link px-3 py-1 border rounded-md text-sm {{ $page == $sliders->currentPage() ? 'bg-blue-600 text-white border-blue-600' : 'border-slate-300 text-slate-600 hover:bg-slate-50' }}">
                {{ $page }}
            </a>
        @endforeach
    </div>
@endif