@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">Familias (<span id="totalCount">{{ $familias->total() }}</span>)</h2>
        <a href="{{ route('familias.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer active:scale-[.98]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Crear Familia
        </a>
    </div>

    <div id="alertContainer"></div>

    {{-- Banner "Todos los productos" --}}
    <div class="bg-white border border-slate-200 rounded-md p-4 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800">Banner — Todos los productos</p>
                <p class="text-xs text-slate-500 mt-0.5">Se muestra en la página de productos cuando no hay ningún filtro seleccionado.</p>
            </div>
            <div id="bannerTodosWidget" class="flex items-center gap-3 flex-shrink-0">
                @if($bannerTodos)
                    <img id="bannerTodosPreview" src="{{ Storage::url($bannerTodos) }}" alt="Banner todos"
                         class="h-14 w-36 object-cover rounded border border-slate-200">
                    <button type="button" id="deleteBannerTodosBtn"
                            class="text-sm text-red-500 hover:text-red-700 border border-red-200 px-3 py-1.5 rounded-md hover:bg-red-50 transition">
                        Eliminar
                    </button>
                @else
                    <img id="bannerTodosPreview" src="" alt="" class="h-14 w-36 object-cover rounded border border-slate-200 hidden">
                    <button type="button" id="deleteBannerTodosBtn"
                            class="text-sm text-red-500 hover:text-red-700 border border-red-200 px-3 py-1.5 rounded-md hover:bg-red-50 transition hidden">
                        Eliminar
                    </button>
                @endif
                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span id="bannerTodosBtnText">{{ $bannerTodos ? 'Cambiar' : 'Subir banner' }}</span>
                    <input type="file" id="bannerTodosInput" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="hidden">
                </label>
            </div>
        </div>
    </div>

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
    </div>

    <p class="text-sm text-slate-500 text-center">
        Página <span id="currentPage">{{ $familias->currentPage() }}</span> de <span id="lastPage">{{ $familias->lastPage() }}</span> —
        Mostrando <span id="firstItem">{{ $familias->firstItem() }}</span>–<span id="lastItem">{{ $familias->lastItem() }}</span> de <span id="totalItems">{{ $familias->total() }}</span> registros
    </p>

    <div class="overflow-x-auto border border-slate-200 rounded-md bg-white shadow-sm">
        <table class="w-full text-sm text-slate-700">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-center font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="orden">Orden</th>
                    <th class="px-4 py-3 font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="titulo">Título</th>
                    <th class="px-4 py-3 text-center font-medium">Destacado</th>
                    <th class="px-4 py-3 text-center font-medium">Visible</th>
                    <th class="px-4 py-3 text-center font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody id="familiasTable" class="divide-y divide-slate-200">
                @include('livewire.familias.partials.table', ['familias' => $familias])
            </tbody>
        </table>
    </div>

    <div id="pagination">
        @include('livewire.familias.partials.pagination', ['familias' => $familias])
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
        searchTimeout = setTimeout(() => loadFamilias(), 300);
    });

    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        this.classList.add('hidden');
        loadFamilias();
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
            loadFamilias();
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const id = btn.dataset.id;
            if (confirm('¿Estás seguro de eliminar esta familia?')) {
                fetch(`/admin/familias/${id}`, {
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
                        loadFamilias();
                        showAlert(data.message, 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error al eliminar la familia', 'error');
                });
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            const page = new URL(url).searchParams.get('page');
            loadFamilias(page);
        }
    });

    function loadFamilias(page = 1) {
        const search = searchInput.value;
        const params = new URLSearchParams();
        params.append('page', page);
        if (search) params.append('search', search);
        params.append('sortField', currentSort.field);
        params.append('sortDirection', currentSort.direction);

        const url = `{{ route('familias.index') }}?${params.toString()}`;

        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('familiasTable').innerHTML = data.html;
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
            showAlert('Error al cargar las familias', 'error');
        });
    }

    function showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alertContainer');
        const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
        alertContainer.innerHTML = `<div class="px-4 py-3 rounded-md ${bgColor} border text-sm">${message}</div>`;
        setTimeout(() => { alertContainer.innerHTML = ''; }, 4000);
    }

    // Banner "Todos los productos"
    const bannerInput   = document.getElementById('bannerTodosInput');
    const bannerPreview = document.getElementById('bannerTodosPreview');
    const bannerDelete  = document.getElementById('deleteBannerTodosBtn');
    const bannerBtnText = document.getElementById('bannerTodosBtnText');
    const csrfToken     = document.querySelector('meta[name="csrf-token"]').content;

    bannerInput.addEventListener('change', function () {
        if (!this.files[0]) return;
        const fd = new FormData();
        fd.append('imagen', this.files[0]);
        fd.append('_token', csrfToken);

        fetch('{{ route("familias.bannerTodos.upload") }}', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    bannerPreview.src = data.url;
                    bannerPreview.classList.remove('hidden');
                    bannerDelete.classList.remove('hidden');
                    bannerBtnText.textContent = 'Cambiar';
                    showAlert('Banner actualizado', 'success');
                } else {
                    showAlert('Error al subir el banner', 'error');
                }
            })
            .catch(() => showAlert('Error al subir el banner', 'error'));

        this.value = '';
    });

    bannerDelete.addEventListener('click', function () {
        if (!confirm('¿Eliminar el banner de "Todos los productos"?')) return;

        fetch('{{ route("familias.bannerTodos.delete") }}', {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bannerPreview.src = '';
                bannerPreview.classList.add('hidden');
                bannerDelete.classList.add('hidden');
                bannerBtnText.textContent = 'Subir banner';
                showAlert('Banner eliminado', 'success');
            }
        })
        .catch(() => showAlert('Error al eliminar el banner', 'error'));
    });
});
</script>
@endsection
