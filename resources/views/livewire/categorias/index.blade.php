@extends('layouts.admin')

@section('content')
<div class="space-y-8 mb-8 bg-white border border-slate-200 rounded-md shadow-sm p-8">
    <h3 class="text-2xl font-semibold text-slate-900 border-b pb-2">Banner de Categorías</h3>

    <form action="{{ route('categorias.banner.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-900 mb-2">Título del Banner</label>
                <input type="text"
                       name="banner_title"
                       value="{{ old('banner_title', $banner->title ?? '') }}"
                       placeholder="Título del banner"
                       class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div class="space-y-4">
                <h4 class="text-sm font-medium text-slate-900">Imagen del Banner</h4>

                @if($banner && $banner->image_banner)
                    <img id="preview-banner_image"
                         src="{{ asset('storage/' . $banner->image_banner) }}"
                         class="w-full max-h-64 object-cover rounded-md border border-slate-200">
                @else
                    <div id="placeholder-banner_image"
                         class="h-32 w-full bg-slate-100 border-2 border-dashed border-slate-300 rounded-md flex items-center justify-center text-slate-500 text-sm">
                        Sin imagen
                    </div>
                    <img id="preview-banner_image" class="hidden w-full max-h-64 object-cover rounded-md border border-slate-200">
                @endif

                <div class="flex gap-2">
                    <input id="file-banner_image" type="file" name="banner_image" accept="image/*" class="hidden" onchange="previewBannerImage(event)">

                    <button type="button"
                            onclick="document.getElementById('file-banner_image').click()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer">
                        {{ $banner && $banner->image_banner ? 'Cambiar Imagen' : 'Subir Imagen' }}
                    </button>

                    @if($banner && $banner->image_banner)
                        <button type="button"
                                onclick="deleteBannerImage()"
                                class="px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition cursor-pointer">
                            Eliminar Imagen
                        </button>
                    @endif

                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 transition cursor-pointer">
                        Guardar Banner
                    </button>
                </div>

                <p class="text-xs text-slate-500">Recomendado 1360×450px • JPG / PNG / WEBP • Máx 3MB.</p>
            </div>
        </div>
    </form>
</div>

<div class="space-y-8 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">Categorías Existentes (<span id="totalCount">{{ $categorias->total() }}</span>)</h2>
        <a href="{{ route('categorias.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer active:scale-[.98]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Crear Categoría
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
        Página <span id="currentPage">{{ $categorias->currentPage() }}</span> de <span id="lastPage">{{ $categorias->lastPage() }}</span> —
        Mostrando <span id="firstItem">{{ $categorias->firstItem() }}</span>–<span id="lastItem">{{ $categorias->lastItem() }}</span> de <span id="totalItems">{{ $categorias->total() }}</span> registros
    </p>

    <div class="overflow-x-auto border border-slate-200 rounded-md bg-white shadow-sm">
        <table class="w-full text-sm text-slate-700">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-center font-medium cursor-pointer hover:text-slate-900 sort-header" data-sort="order">Orden</th>
                    <th class="px-4 py-3 font-medium">Imagen</th>
                    <th class="px-4 py-3 font-medium cursor-pointer text-start hover:text-slate-900 sort-header" data-sort="title">Título</th>
                    <th class="px-4 py-3 font-medium text-center">Subcategorías</th>
                    <th class="px-4 py-3 text-center font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody id="categoriasTable" class="divide-y divide-slate-200">
                @include('livewire.categorias.partials.table', ['categorias' => $categorias])
            </tbody>
        </table>
    </div>

    <div id="pagination">
        @include('livewire.categorias.partials.pagination', ['categorias' => $categorias])
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

        searchTimeout = setTimeout(loadCategorias, 300);
    });

    clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        this.classList.add('hidden');
        searchResults.classList.add('hidden');
        loadCategorias();
    });

    document.querySelectorAll('.sort-header').forEach(header => {
        header.addEventListener('click', function() {
            const sortField = this.dataset.sort;
            loadCategorias(1, sortField);
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            const id = btn.dataset.id;

            if (confirm('¿Estás seguro? Esta acción eliminará todas las subcategorías asociadas.')) {
                fetch(`/admin/categorias/${id}`, {
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
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            const page = new URL(url).searchParams.get('page');
            loadCategorias(page);
        }
    });

    function loadCategorias(page = 1, sortField = 'order', sortDirection = 'asc') {
        const search = searchInput.value;
        const url = `{{ route('categorias.index') }}?page=${page}&search=${encodeURIComponent(search)}&sortField=${sortField}&sortDirection=${sortDirection}`;

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
            document.getElementById('categoriasTable').innerHTML = data.html;
            document.getElementById('pagination').innerHTML = data.pagination;
            
            if (search) {
                searchResults.textContent = `Resultados para "${search}"`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al cargar las categorías', 'error');
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
<script>
    function previewBannerImage(event) {
        const preview = document.getElementById('preview-banner_image');
        const placeholder = document.getElementById('placeholder-banner_image');
        
        if (event.target.files && event.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    }
    
    function deleteBannerImage() {
        if (confirm('¿Estás seguro de eliminar la imagen del banner?')) {
            fetch('{{ route('categorias.banner.delete') }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
    </script>
@endsection