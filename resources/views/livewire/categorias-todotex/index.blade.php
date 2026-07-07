@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">Categorías (<span id="totalCount">{{ $categorias->total() }}</span>)</h2>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('admin.mapeos-erp.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 text-white rounded-md hover:bg-amber-600 transition cursor-pointer active:scale-[.98]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Mapeos ERP
            </a>
            <a href="{{ route('categorias-todotex.fusionar') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 text-white rounded-md hover:bg-violet-700 transition cursor-pointer active:scale-[.98]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                Fusionar
            </a>
            <a href="{{ route('categorias-todotex.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer active:scale-[.98]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Crear Categoría
            </a>
        </div>
    </div>

    <div id="alertContainer"></div>

    <div class="bg-slate-50 border border-slate-200 rounded-md p-4 space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="relative">
                <label class="block text-sm font-medium text-slate-700 mb-1">Buscar</label>
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 mt-6">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text"
                       id="searchInput"
                       placeholder="Buscar por título u orden..."
                       class="w-full pl-10 pr-10 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                <button id="clearSearch" type="button"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 hidden mt-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
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
                <label class="block text-sm font-medium text-slate-700 mb-1">Rubro</label>
                <select id="rubroFilter" class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <option value="">Todos los rubros</option>
                    @foreach($rubros as $rubro)
                        <option value="{{ $rubro->id }}">{{ $rubro->titulo }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="visibleFilter" class="block text-sm font-medium text-slate-700 mb-1">Visibilidad</label>
                <select id="visibleFilter" class="w-full px-3 py-2.5 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm">
                    <option value="">Todas</option>
                    <option value="1">Visibles</option>
                    <option value="0">Ocultas</option>
                </select>
            </div>

            <div class="flex items-end">
                <button id="clearAllFilters" class="px-4 py-2.5 text-sm text-blue-600 hover:text-blue-800 font-medium border border-blue-200 rounded-md hover:bg-blue-50 transition">
                    Limpiar filtros
                </button>
            </div>
        </div>
    </div>

    <p class="text-sm text-slate-500 text-center">
        Página <span id="currentPage">{{ $categorias->currentPage() }}</span> de <span id="lastPage">{{ $categorias->lastPage() }}</span> —
        Mostrando <span id="firstItem">{{ $categorias->firstItem() }}</span>–<span id="lastItem">{{ $categorias->lastItem() }}</span> de <span id="totalItems">{{ $categorias->total() }}</span> registros
    </p>

    <div class="overflow-x-auto border border-slate-200 rounded-md bg-white shadow-sm">
        <table class="w-full text-sm text-slate-700">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-center font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="orden">Orden</th>
                    <th class="px-4 py-3 font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="titulo">Título</th>
                    <th class="px-4 py-3 font-medium">Familia</th>
                    <th class="px-4 py-3 font-medium">Rubros</th>
                    <th class="px-4 py-3 text-center font-medium">Visible</th>
                    <th class="px-4 py-3 text-center font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody id="categoriasTable" class="divide-y divide-slate-200">
                @include('livewire.categorias-todotex.partials.table', ['categorias' => $categorias])
            </tbody>
        </table>
    </div>

    <div id="pagination">
        @include('livewire.categorias-todotex.partials.pagination', ['categorias' => $categorias])
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
    const familiaFilter = document.getElementById('familiaFilter');
    const rubroFilter = document.getElementById('rubroFilter');
    const visibleFilter = document.getElementById('visibleFilter');
    const clearAllFilters = document.getElementById('clearAllFilters');
    let searchTimeout;
    let currentSort = { field: 'orden', direction: 'asc' };

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        clearSearch.classList.toggle('hidden', !this.value);
        searchTimeout = setTimeout(() => loadCategorias(), 300);
    });

    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        this.classList.add('hidden');
        loadCategorias();
    });

    familiaFilter.addEventListener('change', () => loadCategorias());
    rubroFilter.addEventListener('change', () => loadCategorias());
    visibleFilter.addEventListener('change', () => loadCategorias());

    clearAllFilters.addEventListener('click', function() {
        searchInput.value = '';
        clearSearch.classList.add('hidden');
        familiaFilter.value = '';
        rubroFilter.value = '';
        visibleFilter.value = '';
        currentSort = { field: 'orden', direction: 'asc' };
        loadCategorias();
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
            loadCategorias();
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const id = btn.dataset.id;
            if (confirm('¿Estás seguro de eliminar esta categoría?')) {
                fetch(`/admin/categorias-todotex/${id}`, {
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
                        loadCategorias();
                        showAlert(data.message, 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error al eliminar la categoría', 'error');
                });
            }
        }

        // Toggle visible/oculto sin recargar
        if (e.target.closest('.toggle-visible-btn')) {
            const btn = e.target.closest('.toggle-visible-btn');
            const id = btn.dataset.id;
            btn.disabled = true;
            fetch(`/admin/categorias-todotex/${id}/toggle-visible`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizo el botón in-place sin recargar
                    btn.textContent = data.visible ? 'Visible' : 'Oculto';
                    btn.dataset.visible = data.visible ? '1' : '0';
                    btn.title = `Clic para ${data.visible ? 'ocultar' : 'mostrar'}`;
                    btn.classList.remove('bg-green-100', 'text-green-800', 'hover:bg-green-200', 'bg-red-100', 'text-red-800', 'hover:bg-red-200');
                    if (data.visible) {
                        btn.classList.add('bg-green-100', 'text-green-800', 'hover:bg-green-200');
                    } else {
                        btn.classList.add('bg-red-100', 'text-red-800', 'hover:bg-red-200');
                    }
                    showAlert(data.message, 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error al cambiar la visibilidad', 'error');
            })
            .finally(() => { btn.disabled = false; });
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('#pagination a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            const page = new URL(url).searchParams.get('page');
            loadCategorias(page);
        }
    });

    function loadCategorias(page = 1) {
        const params = new URLSearchParams();
        params.append('page', page);
        if (searchInput.value) params.append('search', searchInput.value);
        if (familiaFilter.value) params.append('familia_id', familiaFilter.value);
        if (rubroFilter.value) params.append('rubro_id', rubroFilter.value);
        if (visibleFilter.value !== '') params.append('visible', visibleFilter.value);
        params.append('sortField', currentSort.field);
        params.append('sortDirection', currentSort.direction);

        const url = `{{ route('categorias-todotex.index') }}?${params.toString()}`;

        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('categoriasTable').innerHTML = data.html;
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
            showAlert('Error al cargar las categorías', 'error');
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
