@php
    $current  = $clientes->currentPage();
    $last     = $clientes->lastPage();
    $from     = max(2, $current - 2);
    $to       = min($last - 1, $current + 2);
@endphp

<div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-2">

    {{-- Contador de resultados --}}
    <p class="text-sm text-gray-600 shrink-0">
        Mostrando
        <span class="font-semibold text-gray-900">{{ $clientes->firstItem() }}</span>
        –
        <span class="font-semibold text-gray-900">{{ $clientes->lastItem() }}</span>
        de
        <span class="font-semibold text-gray-900">{{ number_format($clientes->total()) }}</span>
        clientes
    </p>

    @if($last > 1)
    <div class="flex items-center gap-1">

        {{-- Anterior --}}
        @if ($clientes->onFirstPage())
            <span class="inline-flex items-center gap-1 px-3 py-2 text-sm text-gray-300 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed select-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Ant.
            </span>
        @else
            <button onclick="goToPage({{ $current - 1 }})" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-blue-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Ant.
            </button>
        @endif

        {{-- Página 1 --}}
        @if ($current == 1)
            <span class="px-3 py-2 text-sm font-semibold bg-blue-600 text-white rounded-lg shadow-sm">1</span>
        @else
            <button onclick="goToPage(1)" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-blue-600 transition-colors">1</button>
        @endif

        {{-- Elipsis izquierda --}}
        @if ($from > 2)
            <span class="px-2 py-2 text-sm text-gray-400 select-none">…</span>
        @endif

        {{-- Ventana de páginas --}}
        @for ($p = $from; $p <= $to; $p++)
            @if ($p == $current)
                <span class="px-3 py-2 text-sm font-semibold bg-blue-600 text-white rounded-lg shadow-sm">{{ $p }}</span>
            @else
                <button onclick="goToPage({{ $p }})" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-blue-600 transition-colors">{{ $p }}</button>
            @endif
        @endfor

        {{-- Elipsis derecha --}}
        @if ($to < $last - 1)
            <span class="px-2 py-2 text-sm text-gray-400 select-none">…</span>
        @endif

        {{-- Última página --}}
        @if ($last > 1)
            @if ($current == $last)
                <span class="px-3 py-2 text-sm font-semibold bg-blue-600 text-white rounded-lg shadow-sm">{{ $last }}</span>
            @else
                <button onclick="goToPage({{ $last }})" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-blue-600 transition-colors">{{ $last }}</button>
            @endif
        @endif

        {{-- Siguiente --}}
        @if ($clientes->hasMorePages())
            <button onclick="goToPage({{ $current + 1 }})" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-blue-600 transition-colors">
                Sig.
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        @else
            <span class="inline-flex items-center gap-1 px-3 py-2 text-sm text-gray-300 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed select-none">
                Sig.
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif

    </div>

    {{-- Ir a página (útil con muchas páginas) --}}
    @if ($last > 10)
    <div class="flex items-center gap-2 shrink-0">
        <label for="clientes-goto-page" class="text-sm text-gray-600 whitespace-nowrap">Ir a página:</label>
        <input
            id="clientes-goto-page"
            type="number"
            min="1"
            max="{{ $last }}"
            value="{{ $current }}"
            onchange="const p = parseInt(this.value); if(p>=1 && p<={{ $last }}) goToPage(p);"
            class="w-20 px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-center"
        >
        <span class="text-sm text-gray-500">/ {{ $last }}</span>
    </div>
    @endif

    @endif
</div>
