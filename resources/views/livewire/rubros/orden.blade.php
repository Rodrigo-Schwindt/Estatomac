@extends('layouts.admin')

@section('content')
<div class="space-y-6 animate-fadeIn">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Orden de productos — {{ $rubro->titulo }}</h2>
            <p class="text-sm text-slate-500 mt-1">Asigná un número a cada producto para definir su orden dentro de este rubro. Sin número → al final.</p>
        </div>
        <a href="{{ route('rubros.index') }}"
           class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 hover:bg-slate-50 transition cursor-pointer shrink-0">
            ← Volver a Rubros
        </a>
    </div>

    <div id="alertContainer"></div>

    {{-- Filtros --}}
    <div class="bg-white border border-slate-200 rounded-md p-4 shadow-sm flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
            <input type="text" id="searchInput" value="{{ $search }}"
                   placeholder="Buscar por código o título..."
                   class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-md bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
        </div>
        <select id="categoriaFilter"
                class="border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700">
            <option value="">Todas las categorías</option>
            @foreach($rubro->categorias as $cat)
                <option value="{{ $cat->id }}" {{ $categoriaFiltroId == $cat->id ? 'selected' : '' }}>{{ $cat->titulo }}</option>
            @endforeach
        </select>
    </div>

    {{-- Tabla --}}
    <form id="ordenForm">
        @csrf
        <div class="bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 bg-slate-50">
                <p class="text-sm text-slate-500">
                    Mostrando <strong>{{ $productos->firstItem() ?? 0 }}</strong>–<strong>{{ $productos->lastItem() ?? 0 }}</strong>
                    de <strong>{{ $productos->total() }}</strong> productos
                </p>
                <button type="button" id="guardarBtn"
                        class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar orden
                </button>
            </div>

            <table class="w-full text-sm text-slate-700">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-center font-medium w-28">Orden rubro</th>
                        <th class="px-4 py-3 font-medium w-32">Código</th>
                        <th class="px-4 py-3 font-medium">Título</th>
                        <th class="px-4 py-3 font-medium">Categoría</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($productos as $producto)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3 text-center">
                            <input type="number"
                                   name="ordenes[{{ $producto->id }}]"
                                   value="{{ $producto->rubro_orden ?? '' }}"
                                   min="1"
                                   placeholder="—"
                                   class="w-20 text-center border border-slate-300 rounded-md px-2 py-1 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-500 text-xs">{{ $producto->codigo }}</td>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $producto->titulo }}</td>
                        <td class="px-4 py-3 text-slate-500">
                            @foreach($producto->categorias as $cat)
                                <span class="inline-block text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">{{ $cat->titulo }}</span>
                            @endforeach
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-slate-400 text-sm">
                            No hay productos en este rubro.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Paginación --}}
            @if($productos->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-center gap-1">
                @if(!$productos->onFirstPage())
                    <a href="{{ $productos->previousPageUrl() }}"
                       class="px-3 py-1.5 border border-slate-200 rounded-md text-sm text-slate-600 hover:bg-slate-50 transition">← Anterior</a>
                @endif
                @foreach($productos->getUrlRange(1, $productos->lastPage()) as $page => $url)
                    @if($page == $productos->currentPage())
                        <span class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm font-medium">{{ $page }}</span>
                    @elseif(abs($page - $productos->currentPage()) <= 2 || $page == 1 || $page == $productos->lastPage())
                        <a href="{{ $url }}" class="px-3 py-1.5 border border-slate-200 rounded-md text-sm text-slate-600 hover:bg-slate-50 transition">{{ $page }}</a>
                    @elseif(abs($page - $productos->currentPage()) == 3)
                        <span class="px-2 text-slate-400">…</span>
                    @endif
                @endforeach
                @if($productos->hasMorePages())
                    <a href="{{ $productos->nextPageUrl() }}"
                       class="px-3 py-1.5 border border-slate-200 rounded-md text-sm text-slate-600 hover:bg-slate-50 transition">Siguiente →</a>
                @endif
            </div>
            @endif
        </div>
    </form>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput     = document.getElementById('searchInput');
    const categoriaFilter = document.getElementById('categoriaFilter');
    const guardarBtn      = document.getElementById('guardarBtn');
    const csrfToken       = document.querySelector('meta[name="csrf-token"]').content;
    let searchTimeout;

    function applyFilters() {
        const params = new URLSearchParams(window.location.search);
        const search = searchInput.value.trim();
        const categoria = categoriaFilter.value;

        if (search) params.set('search', search); else params.delete('search');
        if (categoria) params.set('categoria_id', categoria); else params.delete('categoria_id');
        params.delete('page');

        window.location.href = window.location.pathname + '?' + params.toString();
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 400);
    });

    categoriaFilter.addEventListener('change', applyFilters);

    guardarBtn.addEventListener('click', function () {
        guardarBtn.disabled = true;
        guardarBtn.textContent = 'Guardando...';

        const form    = document.getElementById('ordenForm');
        const inputs  = form.querySelectorAll('input[name^="ordenes["]');
        const ordenes = {};
        inputs.forEach(input => {
            const match = input.name.match(/ordenes\[(\d+)\]/);
            if (match) ordenes[match[1]] = input.value;
        });

        fetch('{{ route("rubros.guardarOrden", $rubro->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ ordenes }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
            } else {
                showAlert('Error al guardar el orden.', 'error');
            }
        })
        .catch(() => showAlert('Error de conexión.', 'error'))
        .finally(() => {
            guardarBtn.disabled = false;
            guardarBtn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Guardar orden`;
        });
    });

    function showAlert(message, type) {
        const container = document.getElementById('alertContainer');
        const cls = type === 'success'
            ? 'bg-green-50 border-green-200 text-green-700'
            : 'bg-red-50 border-red-200 text-red-700';
        container.innerHTML = `<div class="px-4 py-3 rounded-md ${cls} border text-sm">${message}</div>`;
        setTimeout(() => { container.innerHTML = ''; }, 4000);
    }
});
</script>
@endsection
