@extends('layouts.admin')

@section('content')
<div class="space-y-6 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">
            Productos (<span id="totalCount">{{ $productos->total() }}</span>)
        </h2>
        <div class="flex flex-col sm:flex-row gap-2">
            <form action="{{ route('admin.imports.erp-zip') }}" method="POST"
                  onsubmit="return confirm('Esto va a reemplazar TODAS las tablas del ERP (clientes, productos, listas de precios, categorías, etc.) con el contenido del .zip más reciente del FTP. Si algo falla se hace rollback. ¿Continuar?')"
                  class="inline">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 text-white rounded-md hover:bg-amber-600 transition cursor-pointer active:scale-[.98]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Procesar ZIP del ERP
                </button>
            </form>
            <a href="{{ route('productos-todotex.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer active:scale-[.98]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Crear Producto
            </a>
        </div>
    </div>

    <div id="alertContainer">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md mb-2">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md mb-2 whitespace-pre-wrap">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <div class="bg-slate-50 border border-slate-200 rounded-md p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900">Filtros</h3>
            <button id="clearAllFilters" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Limpiar todos los filtros
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="relative">
                <label class="block text-sm font-medium text-slate-700 mb-1">Buscar</label>
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 mt-6">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text"
                       id="searchInput"
                       placeholder="Título, código u orden..."
                       class="w-full pl-10 pr-10 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                <button id="clearSearch" type="button"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 hidden mt-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="relative">
                <label class="block text-sm font-medium text-slate-700 mb-1">C&oacute;digo exacto</label>
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 mt-6">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </span>
                <input type="text"
                       id="codigoInput"
                       placeholder="Ej: ABC123"
                       class="w-full pl-10 pr-10 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                <button id="clearCodigo" type="button"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 hidden mt-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Rango de c&oacute;digo</label>
                <div class="flex gap-2">
                    <input type="number"
                           id="codigoDesdeInput"
                           aria-label="Código desde"
                           placeholder="Desde"
                           min="0"
                           class="w-1/2 px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <input type="number"
                           id="codigoHastaInput"
                           aria-label="Código hasta"
                           placeholder="Hasta"
                           min="0"
                           class="w-1/2 px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                </div>
                <p class="text-xs text-slate-400 mt-1">Ej: 1000 a 2000</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Familia</label>
                <select id="familiaFilter" class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <option value="">Todas las familias</option>
                    @foreach($familias as $familia)
                        <option value="{{ $familia->id }}">{{ $familia->titulo }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Categoría</label>
                <select id="categoriaFilter" class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}">
                            {{ $cat->titulo }}{{ $cat->rubro ? ' - ' . $cat->rubro->titulo : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
                <select id="visibleFilter" class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <option value="">Todos</option>
                    <option value="1">Visible</option>
                    <option value="0">Oculto</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Destacado</label>
                <select id="destacadoFilter" class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <option value="">Todos</option>
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Símbolos</label>
                <select id="simboloFilter" class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <option value="">Todos</option>
                    @foreach($simbolos as $simbolo)
                        <option value="{{ $simbolo->id }}" @selected((string) request('simbolo_id', '') === (string) $simbolo->id)>
                            {{ html_entity_decode($simbolo->display_entity, ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Mostrar</label>
                <select id="perPageSelect" class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <option value="10">10 por página</option>
                    <option value="25" selected>25 por página</option>
                    <option value="50">50 por página</option>
                    <option value="100">100 por página</option>
                </select>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between text-sm text-slate-600 bg-slate-50 px-4 py-3 rounded-md border border-slate-200">
        <div>
            Mostrando <span id="firstItem" class="font-semibold text-slate-900">{{ $productos->firstItem() }}</span> -
            <span id="lastItem" class="font-semibold text-slate-900">{{ $productos->lastItem() }}</span> de
            <span id="totalItems" class="font-semibold text-slate-900">{{ $productos->total() }}</span> productos
        </div>
        <div>
            Página <span id="currentPage" class="font-semibold text-slate-900">{{ $productos->currentPage() }}</span> de
            <span id="lastPage" class="font-semibold text-slate-900">{{ $productos->lastPage() }}</span>
        </div>
    </div>

    <div class="overflow-x-auto border border-slate-200 rounded-md bg-white shadow-sm">
        <table class="w-full text-sm text-slate-700">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-center font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="orden">Orden</th>
                    <th class="px-4 py-3 text-center font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="pk_externa">PK Producto</th>
                    <th class="px-4 py-3 text-center font-medium">Simbolo</th>
                    <th class="px-4 py-3 text-center font-medium">Foto</th>
                    <th class="px-4 py-3 font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="codigo">Código</th>
                    <th class="px-4 py-3 font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="titulo">Título</th>
                    <th class="px-4 py-3 font-medium">Categorías</th>
                    <th class="px-4 py-3 text-center font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="precio_unitario">Precio Unit.</th>
                    <th class="px-4 py-3 text-center font-medium">Estado</th>
                    <th class="px-4 py-3 text-center font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody id="productosTable" class="divide-y divide-slate-200">
                @include('livewire.productos-todotex.partials.table', ['productos' => $productos])
            </tbody>
        </table>
    </div>

    <div id="pagination">
        @include('livewire.productos-todotex.partials.pagination', ['productos' => $productos])
    </div>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const codigoInput = document.getElementById('codigoInput');
    const clearCodigo = document.getElementById('clearCodigo');
    const codigoDesdeInput = document.getElementById('codigoDesdeInput');
    const codigoHastaInput = document.getElementById('codigoHastaInput');
    const familiaFilter = document.getElementById('familiaFilter');
    const categoriaFilter = document.getElementById('categoriaFilter');
    const visibleFilter = document.getElementById('visibleFilter');
    const destacadoFilter = document.getElementById('destacadoFilter');
    const simboloFilter = document.getElementById('simboloFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const clearAllFilters = document.getElementById('clearAllFilters');

    let searchTimeout;
    let currentSort = { field: 'orden', direction: 'asc' };

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        clearSearch.classList.toggle('hidden', !this.value);
        searchTimeout = setTimeout(() => loadProductos(), 300);
    });

    codigoInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        clearCodigo.classList.toggle('hidden', !this.value);
        searchTimeout = setTimeout(() => loadProductos(), 300);
    });

    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        this.classList.add('hidden');
        loadProductos();
    });

    clearCodigo.addEventListener('click', function() {
        codigoInput.value = '';
        this.classList.add('hidden');
        loadProductos();
    });

    // Filtro por rango de código — disparo con debounce al tipear en cualquiera de los dos
    [codigoDesdeInput, codigoHastaInput].forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => loadProductos(), 400);
        });
    });

    [familiaFilter, categoriaFilter, visibleFilter, destacadoFilter, simboloFilter, perPageSelect].forEach(filter => {
        filter.addEventListener('change', () => loadProductos());
    });

    clearAllFilters.addEventListener('click', function() {
        searchInput.value = '';
        codigoInput.value = '';
        codigoDesdeInput.value = '';
        codigoHastaInput.value = '';
        clearSearch.classList.add('hidden');
        clearCodigo.classList.add('hidden');
        familiaFilter.value = '';
        categoriaFilter.value = '';
        visibleFilter.value = '';
        destacadoFilter.value = '';
        simboloFilter.value = '';
        perPageSelect.value = '25';
        currentSort = { field: 'orden', direction: 'asc' };
        loadProductos();
    });

    document.querySelectorAll('.sort-header').forEach(header => {
        header.addEventListener('click', function() {
            const sortField = this.dataset.sort;
            if (currentSort.field === sortField) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.field = sortField;
                currentSort.direction = 'asc';
            }
            loadProductos();
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.toggle-visible-btn')) {
            const btn = e.target.closest('.toggle-visible-btn');
            const id = btn.dataset.id;
            fetch(`/admin/productos-todotex/${id}/toggle-visible`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const visible = data.visible;
                    btn.dataset.visible = visible ? '1' : '0';
                    btn.textContent = visible ? 'Visible' : 'Oculto';
                    btn.title = visible ? 'Clic para ocultar' : 'Clic para mostrar';
                    btn.className = btn.className.replace(/bg-(green|red)-100 text-(green|red)-800 hover:bg-(green|red)-200/,
                        visible
                            ? 'bg-green-100 text-green-800 hover:bg-green-200'
                            : 'bg-red-100 text-red-800 hover:bg-red-200');
                    showAlert(data.message, 'success');
                }
            })
            .catch(() => showAlert('Error al cambiar el estado', 'error'));
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const id = btn.dataset.id;
            if (confirm('¿Estás seguro? Esta acción eliminará el producto y todas sus imágenes.')) {
                fetch(`/admin/productos-todotex/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadProductos();
                        showAlert(data.message, 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error al eliminar el producto', 'error');
                });
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            const page = new URL(url).searchParams.get('page');
            loadProductos(page);
        }
    });

    function loadProductos(page = 1) {
        const params = new URLSearchParams();
        params.append('page', page);
        if (searchInput.value) params.append('search', searchInput.value);
        if (codigoInput.value.trim()) params.append('codigo', codigoInput.value.trim());
        if (codigoDesdeInput.value.trim()) params.append('codigo_desde', codigoDesdeInput.value.trim());
        if (codigoHastaInput.value.trim()) params.append('codigo_hasta', codigoHastaInput.value.trim());
        if (familiaFilter.value) params.append('familia_id', familiaFilter.value);
        if (categoriaFilter.value) params.append('categoria_id', categoriaFilter.value);
        if (simboloFilter.value) params.append('simbolo_id', simboloFilter.value);
        if (visibleFilter.value) params.append('visible', visibleFilter.value);
        if (destacadoFilter.value) params.append('destacado', destacadoFilter.value);
        if (perPageSelect.value) params.append('per_page', perPageSelect.value);
        params.append('sortField', currentSort.field);
        params.append('sortDirection', currentSort.direction);

        const url = `{{ route('productos-todotex.index') }}?${params.toString()}`;

        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('productosTable').innerHTML = data.html;
            document.getElementById('pagination').innerHTML = data.pagination;
            document.getElementById('totalCount').textContent = data.stats.total;
            document.getElementById('firstItem').textContent = data.stats.from || 0;
            document.getElementById('lastItem').textContent = data.stats.to || 0;
            document.getElementById('totalItems').textContent = data.stats.total;
            document.getElementById('currentPage').textContent = data.stats.current_page;
            document.getElementById('lastPage').textContent = data.stats.last_page;
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al cargar los productos', 'error');
        });
    }

    function showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alertContainer');
        const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
        alertContainer.innerHTML = `<div class="px-4 py-3 rounded-md ${bgColor} border text-sm">${message}</div>`;
        setTimeout(() => { alertContainer.innerHTML = ''; }, 4000);
    }
});
</script>
@endsection
