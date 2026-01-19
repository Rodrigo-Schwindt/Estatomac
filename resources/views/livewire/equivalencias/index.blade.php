{{-- resources/views/admin/equivalencias/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">Equivalencias Existentes (<span id="totalCount">{{ $equivalencias->total() }}</span>)</h2>
        <a href="{{ route('equivalencias.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer active:scale-[.98]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Crear Equivalencia
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
                   placeholder="Buscar por título, código u orden..."
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

        <p id="searchResults" class="mt-2 text-sm text-slate-600 hidden"></p>
    </div>

    <p class="text-sm text-slate-500 text-center">
        Página <span id="currentPage">{{ $equivalencias->currentPage() }}</span> de <span id="lastPage">{{ $equivalencias->lastPage() }}</span> —
        Mostrando <span id="firstItem">{{ $equivalencias->firstItem() }}</span>–<span id="lastItem">{{ $equivalencias->lastItem() }}</span> de <span id="totalItems">{{ $equivalencias->total() }}</span> registros
    </p>

    <div class="overflow-x-auto border border-slate-200 rounded-md bg-white shadow-sm">
        <table class="w-full text-sm text-slate-700">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-center font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="order">Orden</th>
                    <th class="px-4 py-3 font-medium cursor-pointer text-start hover:text-slate-900 sort-header" data-sort="title">Título</th>
                    <th class="px-4 py-3 font-medium cursor-pointer text-start hover:text-slate-900 sort-header" data-sort="code">Código</th>
                    <th class="px-4 py-3 font-medium">Producto</th>
                    <th class="px-4 py-3 text-center font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody id="equivalenciasTable" class="divide-y divide-slate-200">
                @include('livewire.equivalencias.partials.table', ['equivalencias' => $equivalencias])
            </tbody>
        </table>
    </div>

    <div id="pagination">
        @include('livewire.equivalencias.partials.pagination', ['equivalencias' => $equivalencias])
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
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        clearSearch.classList.toggle('hidden', !this.value);
        searchResults.classList.toggle('hidden', !this.value);

        searchTimeout = setTimeout(loadEquivalencias, 300);
    });

    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        this.classList.add('hidden');
        searchResults.classList.add('hidden');
        loadEquivalencias();
    });

    document.querySelectorAll('.sort-header').forEach(header => {
        header.addEventListener('click', function() {
            const sortField = this.dataset.sort;
            loadEquivalencias(1, sortField);
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const id = btn.dataset.id;

            if (confirm('¿Estás seguro?')) {
                fetch(`/admin/equivalencias/${id}`, {
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
                        loadEquivalencias();
                        showAlert(data.message, 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error al eliminar la equivalencia', 'error');
                });
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            const page = new URL(url).searchParams.get('page');
            loadEquivalencias(page);
        }
    });

    function loadEquivalencias(page = 1, sortField = 'order', sortDirection = 'asc') {
        const search = searchInput.value;
        const url = `{{ route('equivalencias.index') }}?page=${page}&search=${encodeURIComponent(search)}&sortField=${sortField}&sortDirection=${sortDirection}`;

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
            document.getElementById('equivalenciasTable').innerHTML = data.html;
            document.getElementById('pagination').innerHTML = data.pagination;
            
            if (search) {
                searchResults.textContent = `Resultados para "${search}"`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al cargar las equivalencias', 'error');
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