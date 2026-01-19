@if($precios->hasPages())
    <div class="flex items-center justify-between pt-5 border-t border-slate-200">
        {{-- Botón Anterior --}}
        @if($precios->onFirstPage())
            <span class="px-4 py-2 border border-slate-200 rounded-md text-slate-400 text-sm cursor-not-allowed">
                ← Anterior
            </span>
        @else
            <a href="{{ $precios->previousPageUrl() }}" 
               class="pagination-link px-4 py-2 border border-slate-300 rounded-md text-slate-600 text-sm hover:bg-slate-50 transition">
                ← Anterior
            </a>
        @endif

        <span class="text-sm text-slate-500 font-medium">
            Página {{ $precios->currentPage() }} de {{ $precios->lastPage() }}
        </span>

        {{-- Botón Siguiente --}}
        @if($precios->hasMorePages())
            <a href="{{ $precios->nextPageUrl() }}" 
               class="pagination-link px-4 py-2 border border-slate-300 rounded-md text-slate-600 text-sm hover:bg-slate-50 transition">
                Siguiente →
            </a>
        @else
            <span class="px-4 py-2 border border-slate-200 rounded-md text-slate-400 text-sm cursor-not-allowed">
                Siguiente →
            </span>
        @endif
    </div>

    {{-- Selector de Números de Página --}}
    <div class="flex justify-center gap-1 mt-4 flex-wrap">
        @foreach ($precios->getUrlRange(1, $precios->lastPage()) as $page => $url)
            <a href="{{ $url }}" 
               class="pagination-link px-3.5 py-1.5 border rounded-md text-sm transition-colors {{ $page == $precios->currentPage() ? 'bg-blue-600 text-white border-blue-600' : 'border-slate-300 text-slate-600 hover:bg-slate-50' }}">
                {{ $page }}
            </a>
        @endforeach
    </div>
@endif