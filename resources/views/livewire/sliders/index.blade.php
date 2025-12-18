@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">Sliders Existentes (<span id="totalCount">{{ $sliders->total() }}</span>)</h2>
        <a href="{{ route('sliders.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer active:scale-[.98]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Crear Slider
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
                   placeholder="Buscar por título u orden..."
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
        Página <span id="currentPage">{{ $sliders->currentPage() }}</span> de <span id="lastPage">{{ $sliders->lastPage() }}</span> —
        Mostrando <span id="firstItem">{{ $sliders->firstItem() }}</span>–<span id="lastItem">{{ $sliders->lastItem() }}</span> de <span id="totalItems">{{ $sliders->total() }}</span> registros
    </p>

    <div class="overflow-x-auto border border-slate-200 rounded-md bg-white shadow-sm">
        <table class="w-full text-sm text-slate-700">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-center font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="orden">Orden</th>
                    <th class="px-4 py-3 font-medium">Archivo</th>
                    <th class="px-4 py-3 font-medium cursor-pointer text-start hover:text-slate-900 sort-header" data-sort="title">Título / Descripción</th>
                    <th class="px-4 py-3 text-center font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody id="slidersTable" class="divide-y divide-slate-200">
                @include('livewire.sliders.partials.table', ['sliders' => $sliders])
            </tbody>
        </table>
    </div>

    <div id="pagination">
        @include('livewire.sliders.partials.pagination', ['sliders' => $sliders])
    </div>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
.line-clamp-2 { display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    // Search
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        clearSearch.classList.toggle('hidden', !this.value);
        searchResults.classList.toggle('hidden', !this.value);

        searchTimeout = setTimeout(loadSliders, 300);
    });

    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        this.classList.add('hidden');
        searchResults.classList.add('hidden');
        loadSliders();
    });

    // Sort
    document.querySelectorAll('.sort-header').forEach(header => {
        header.addEventListener('click', function() {
            const sortField = this.dataset.sort;
            loadSliders(1, sortField);
        });
    });

    // Delete
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const id = btn.dataset.id;

            if (confirm('¿Estás seguro?')) {
                fetch(`/admin/sliders/${id}`, {
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
                        loadSliders();
                        showAlert(data.message, 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error al eliminar el slider', 'error');
                });
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            const page = new URL(url).searchParams.get('page');
            loadSliders(page);
        }
    });

    function loadSliders(page = 1, sortField = 'orden', sortDirection = 'asc') {
        const search = searchInput.value;
        const url = `{{ route('sliders.index') }}?page=${page}&search=${encodeURIComponent(search)}&sortField=${sortField}&sortDirection=${sortDirection}`;

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
            document.getElementById('slidersTable').innerHTML = data.html;
            document.getElementById('pagination').innerHTML = data.pagination;
            
            if (search) {
                const matches = new URL(url).searchParams.get('page') === '1' ? '1 resultado' : 'resultados';
                searchResults.textContent = `${matches} para "${search}"`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al cargar los sliders', 'error');
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