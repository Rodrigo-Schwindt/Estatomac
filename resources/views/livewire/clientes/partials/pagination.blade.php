@if ($clientes->hasPages())
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Mostrando {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de {{ $clientes->total() }} resultados
        </div>
        
        <div class="flex gap-2">
            @if ($clientes->onFirstPage())
                <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">Anterior</span>
            @else
                <button onclick="goToPage({{ $clientes->currentPage() - 1 }})" class="px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded">
                    Anterior
                </button>
            @endif

            @foreach ($clientes->getUrlRange(1, $clientes->lastPage()) as $page => $url)
                @if ($page == $clientes->currentPage())
                    <span class="px-3 py-2 text-sm bg-blue-500 text-white rounded">{{ $page }}</span>
                @else
                    <button onclick="goToPage({{ $page }})" class="px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded">
                        {{ $page }}
                    </button>
                @endif
            @endforeach

            @if ($clientes->hasMorePages())
                <button onclick="goToPage({{ $clientes->currentPage() + 1 }})" class="px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded">
                    Siguiente
                </button>
            @else
                <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">Siguiente</span>
            @endif
        </div>
    </div>
@endif