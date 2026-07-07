@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">Colores (<span id="totalCount">{{ $colores->total() }}</span>)</h2>
        <a href="{{ route('colores-todotex.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer active:scale-[.98]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Crear Color
        </a>
    </div>

    <div id="alertContainer"></div>

    <div class="bg-white border border-slate-200 rounded-md p-4 shadow-sm">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
            <input type="text"
                   id="searchInput"
                   placeholder="Buscar por título, código o orden..."
                   class="w-full pl-10 pr-10 py-2.5 border border-slate-300 rounded-md bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-700 text-sm transition">
            <button id="clearSearch"
                    type="button"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 hidden">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <p class="text-sm text-slate-500 text-center">
        Página <span id="currentPage">{{ $colores->currentPage() }}</span> de <span id="lastPage">{{ $colores->lastPage() }}</span> -
        Mostrando <span id="firstItem">{{ $colores->firstItem() }}</span>-<span id="lastItem">{{ $colores->lastItem() }}</span> de <span id="totalItems">{{ $colores->total() }}</span> registros
    </p>

    <div class="overflow-x-auto border border-slate-200 rounded-md bg-white shadow-sm">
        <table class="w-full text-sm text-slate-700">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-center font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="orden">Orden</th>
                    <th class="px-4 py-3 font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="titulo">Título</th>
                    <th class="px-4 py-3 text-center font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="codigo_color">Código color</th>
                    <th class="px-4 py-3 text-center font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody id="coloresTable" class="divide-y divide-slate-200">
                @include('livewire.colores-todotex.partials.table', ['colores' => $colores])
            </tbody>
        </table>
    </div>

    <div id="pagination">
        @include('livewire.colores-todotex.partials.pagination', ['colores' => $colores])
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
    let searchTimeout;
    let currentSort = { field: 'orden', direction: 'asc' };

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        clearSearch.classList.toggle('hidden', !this.value);
        searchTimeout = setTimeout(() => loadColores(), 300);
    });

    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        this.classList.add('hidden');
        loadColores();
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
            loadColores();
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const id = btn.dataset.id;
            if (confirm('¿Estás seguro de eliminar este color?')) {
                fetch(`/admin/colores-todotex/${id}`, {
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
                        loadColores();
                        showAlert(data.message, 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error al eliminar el color', 'error');
                });
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            const page = new URL(url).searchParams.get('page');
            loadColores(page);
        }
    });

    function loadColores(page = 1) {
        const search = searchInput.value;
        const params = new URLSearchParams();
        params.append('page', page);
        if (search) params.append('search', search);
        params.append('sortField', currentSort.field);
        params.append('sortDirection', currentSort.direction);

        const url = `{{ route('colores-todotex.index') }}?${params.toString()}`;

        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('coloresTable').innerHTML = data.html;
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
            showAlert('Error al cargar los colores', 'error');
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
