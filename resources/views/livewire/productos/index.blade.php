@extends('layouts.admin')

@section('content')
<div class="space-y-6 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">
            Productos (<span id="totalCount">{{ $productos->total() }}</span>)
        </h2>
        <div class="flex gap-3">
            <a href="{{ route('productos.import.form') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white rounded-md hover:bg-green-700 transition cursor-pointer active:scale-[.98]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Actualizar Producto
            </a>
            <a href="{{ route('productos.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer active:scale-[.98]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Crear Producto
            </a>
        </div>
    </div>

    <div id="alertContainer"></div>

    <!-- Filtros Avanzados -->
    <div class="bg-slate-50 border border-slate-200 rounded-md p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900">Filtros</h3>
            <button id="clearAllFilters" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Limpiar todos los filtros
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Búsqueda -->
            <div class="relative">
                <label class="block text-sm font-medium text-slate-700 mb-1">Buscar</label>
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 mt-6">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text"
                       id="searchInput"
                       placeholder="Título, código u orden..."
                       class="w-full pl-10 pr-10 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                <button id="clearSearch"
                        type="button"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 hidden mt-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Categoría -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Categoría</label>
                <select id="categoriaFilter" class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->title }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Estado Visible -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
                <select id="visibleFilter" class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <option value="">Todos</option>
                    <option value="1">Publico</option>
                    <option value="0">Oculto</option>
                </select>
            </div>

            <!-- Tipo (Nuevo/Recambio) -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tipo</label>
                <select id="nuevoFilter" class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <option value="">Todos</option>
                    <option value="nuevo">Nuevo</option>
                    <option value="recambio">Recambio</option>
                </select>
            </div>

            <!-- Importados -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Importados</label>
                <select id="importadosFilter" class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <option value="">Todos</option>
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
            </div>

            <!-- Destacado -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Destacado</label>
                <select id="destacadoFilter" class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <option value="">Todos</option>
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
            </div>

            <!-- Oferta -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">En oferta</label>
                <select id="ofertaFilter" class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <option value="">Todos</option>
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
            </div>

            <!-- Items por página -->
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

    <!-- Estadísticas -->
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

    <!-- Tabla -->
    <div class="overflow-x-auto border border-slate-200 rounded-md bg-white shadow-sm">
        <table class="w-full text-sm text-slate-700">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-center font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="order">
                        <div class="flex items-center justify-center gap-1">
                            Orden
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                        </div>
                    </th>
                    <th class="px-4 py-3 font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="code">
                        <div class="flex items-center gap-1">
                            Código
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                        </div>
                    </th>
                    <th class="px-4 py-3 font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="title">
                        <div class="flex items-center gap-1">
                            Título
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                        </div>
                    </th>
                    <th class="px-4 py-3 font-medium">Categoría</th>
                    <th class="px-4 py-3 font-medium text-center cursor-pointer hover:text-slate-900 sort-header" data-sort="precio">
                        <div class="flex items-center justify-center gap-1">
                            Precio
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                        </div>
                    </th>
                    <th class="px-4 py-3 font-medium text-center">Etiquetas</th>
                    <th class="px-4 py-3 text-center font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody id="productosTable" class="divide-y divide-slate-200">
                @include('livewire.productos.partials.table', ['productos' => $productos])
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div id="pagination">
        @include('livewire.productos.partials.pagination', ['productos' => $productos])
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
    const categoriaFilter = document.getElementById('categoriaFilter');
    const visibleFilter = document.getElementById('visibleFilter');
    const nuevoFilter = document.getElementById('nuevoFilter');
    const importadosFilter = document.getElementById('importadosFilter');
    const destacadoFilter = document.getElementById('destacadoFilter');
    const ofertaFilter = document.getElementById('ofertaFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const clearAllFilters = document.getElementById('clearAllFilters');
    
    let searchTimeout;
    let currentSort = { field: 'order', direction: 'asc' };

    // Búsqueda con debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        clearSearch.classList.toggle('hidden', !this.value);
        searchTimeout = setTimeout(() => loadProductos(), 300);
    });

    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        this.classList.add('hidden');
        loadProductos();
    });

    // Filtros
    [categoriaFilter, visibleFilter, nuevoFilter, importadosFilter, destacadoFilter, ofertaFilter, perPageSelect].forEach(filter => {
        filter.addEventListener('change', () => loadProductos());
    });

    // Limpiar todos los filtros
    clearAllFilters.addEventListener('click', function() {
        searchInput.value = '';
        clearSearch.classList.add('hidden');
        categoriaFilter.value = '';
        visibleFilter.value = '';
        nuevoFilter.value = '';
        importadosFilter.value = '';
        destacadoFilter.value = '';
        ofertaFilter.value = '';
        perPageSelect.value = '25';
        currentSort = { field: 'order', direction: 'asc' };
        loadProductos();
    });

    // Ordenamiento
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

    // Eliminar producto
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const id = btn.dataset.id;

            if (confirm('¿Estás seguro? Esta acción eliminará el producto y todas sus imágenes.')) {
                fetch(`/admin/productos/${id}`, {
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

    // Paginación
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            const page = new URL(url).searchParams.get('page');
            loadProductos(page);
        }
    });

    function loadProductos(page = 1) {
    const search = searchInput.value;
    const categoria_id = categoriaFilter.value;
    const visible = visibleFilter.value;
    const nuevo = nuevoFilter.value;
    const importados = importadosFilter.value;
    const destacado = destacadoFilter.value;
    const oferta = ofertaFilter.value;
    const perPage = perPageSelect.value;

    const params = new URLSearchParams();
    
    params.append('page', page);
    
    // Solo agregar parámetros con valores
    if (search) params.append('search', search);
    if (categoria_id) params.append('categoria_id', categoria_id);
    if (visible) params.append('visible', visible);
    if (nuevo) params.append('nuevo', nuevo);
    if (importados) params.append('importados', importados);
    if (destacado) params.append('destacado', destacado);
    if (oferta) params.append('oferta', oferta);
    if (perPage) params.append('per_page', perPage);
    
    params.append('sortField', currentSort.field);
    params.append('sortDirection', currentSort.direction);

    const url = `{{ route('productos.index') }}?${params.toString()}`;

    fetch(url, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        document.getElementById('productosTable').innerHTML = data.html;
        document.getElementById('pagination').innerHTML = data.pagination;
        
        // Actualizar estadísticas
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
        
        alertContainer.innerHTML = `
            <div class="px-4 py-3 rounded-md ${bgColor} border text-sm">
                ${message}
            </div>
        `;

        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 4000);
    }
});
</script>
@endsection