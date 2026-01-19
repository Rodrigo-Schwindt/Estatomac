@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fadeIn">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-slate-900">Crear Producto</h2>
        <a href="{{ route('productos.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-6">
        @csrf

        <div class="space-y-6">
            <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">Información Básica</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-2">
                        Título <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('title') border-red-500 @enderror"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-slate-700 mb-2">Código</label>
                    <input type="text" 
                           name="code" 
                           id="code" 
                           value="{{ old('code') }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="order" class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
                    <input type="text" 
                           name="order" 
                           id="order" 
                           value="{{ old('order') }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('order') border-red-500 @enderror">
                    @error('order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- MARCAS CON CHECKBOXES -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-3">Marcas</label>
                    
                    <div class="relative">
                        <input type="text" 
                               id="searchMarcas" 
                               placeholder="Buscar marcas..."
                               class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 mb-3">
                        
                        <div class="border border-slate-300 rounded-md p-4 max-h-64 overflow-y-auto bg-slate-50">
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3" id="marcasContainer">
                                @foreach($marcas as $marca)
                                    <label class="flex items-center gap-2 p-2 rounded-md hover:bg-white cursor-pointer transition marca-item">
                                        <input type="checkbox" 
                                               name="marcas[]" 
                                               value="{{ $marca->id }}"
                                               {{ (is_array(old('marcas')) && in_array($marca->id, old('marcas'))) ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-2 focus:ring-blue-600">
                                        <span class="text-sm text-slate-700 marca-title">{{ $marca->title }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div id="noMarcasFound" class="hidden text-center text-slate-500 text-sm py-4">
                                No se encontraron marcas
                            </div>
                        </div>
                    </div>
                    
                    <div id="selectedMarcasCount" class="mt-2 text-xs text-slate-600"></div>
                    
                    @error('marcas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- MODELOS CON CHECKBOXES -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-3">Modelos</label>
                    
                    <div class="relative">
                        <input type="text" 
                               id="searchModelos" 
                               placeholder="Buscar modelos..."
                               class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 mb-3">
                        
                        <div class="border border-slate-300 rounded-md p-4 max-h-64 overflow-y-auto bg-slate-50">
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3" id="modelosContainer">
                                @foreach($modelos as $modelo)
                                    <label class="flex items-center gap-2 p-2 rounded-md hover:bg-white cursor-pointer transition modelo-item">
                                        <input type="checkbox" 
                                               name="modelos[]" 
                                               value="{{ $modelo->id }}"
                                               {{ (is_array(old('modelos')) && in_array($modelo->id, old('modelos'))) ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-2 focus:ring-blue-600">
                                        <span class="text-sm text-slate-700 modelo-title">{{ $modelo->title }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div id="noModelosFound" class="hidden text-center text-slate-500 text-sm py-4">
                                No se encontraron modelos
                            </div>
                        </div>
                    </div>
                    
                    <div id="selectedModelosCount" class="mt-2 text-xs text-slate-600"></div>
                    
                    @error('modelos')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="categoria_id" class="block text-sm font-medium text-slate-700 mb-2">Categoría</label>
                    <select name="categoria_id" 
                            id="categoria_id" 
                            class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('categoria_id') border-red-500 @enderror">
                        <option value="">Seleccionar categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="visible" class="block text-sm font-medium text-slate-700 mb-2">
                        Visibilidad <span class="text-red-500">*</span>
                    </label>
                    <select name="visible" 
                            id="visible" 
                            class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('visible') border-red-500 @enderror"
                            required>
                        <option value="1" {{ old('visible', '1') == '1' ? 'selected' : '' }}>Visible</option>
                        <option value="0" {{ old('visible') == '0' ? 'selected' : '' }}>Oculto</option>
                    </select>
                    @error('visible')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nuevo" class="block text-sm font-medium text-slate-700 mb-2">
                        Tipo <span class="text-red-500">*</span>
                    </label>
                    <select name="nuevo" 
                            id="nuevo" 
                            class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('nuevo') border-red-500 @enderror"
                            required>
                        <option value="nuevo" {{ old('nuevo', 'nuevo') == 'nuevo' ? 'selected' : '' }}>Nuevo</option>
                        <option value="recambio" {{ old('nuevo') == 'recambio' ? 'selected' : '' }}>Recambio</option>
                    </select>
                    @error('nuevo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="destacado" class="block text-sm font-medium text-slate-700 mb-2">
                        Destacado <span class="text-red-500">*</span>
                    </label>
                    <select name="destacado" 
                            id="destacado" 
                            class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('destacado') border-red-500 @enderror"
                            required>
                        <option value="0" {{ old('destacado', '0') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('destacado') == '1' ? 'selected' : '' }}>Sí</option>
                    </select>
                    @error('destacado')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="oferta" class="block text-sm font-medium text-slate-700 mb-2">
                        Oferta <span class="text-red-500">*</span>
                    </label>
                    <select name="oferta" 
                            id="oferta" 
                            class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('oferta') border-red-500 @enderror"
                            required>
                        <option value="0" {{ old('oferta', '0') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('oferta') == '1' ? 'selected' : '' }}>Sí</option>
                    </select>
                    @error('oferta')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="importados" class="block text-sm font-medium text-slate-700 mb-2">
                        Importados <span class="text-red-500">*</span>
                    </label>
                    <select name="importados" 
                            id="importados" 
                            class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('importados') border-red-500 @enderror"
                            required>
                        <option value="0" {{ old('importados', '0') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('importados') == '1' ? 'selected' : '' }}>Sí</option>
                    </select>
                    @error('importados')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="precio" class="block text-sm font-medium text-slate-700 mb-2">
                        Precio <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="precio" 
                           id="precio" 
                           step="0.01"
                           min="0"
                           value="{{ old('precio', '0') }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('precio') border-red-500 @enderror"
                           required>
                    @error('precio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="descuento" class="block text-sm font-medium text-slate-700 mb-2">Descuento</label>
                    <input type="number" 
                           name="descuento" 
                           id="descuento" 
                           step="0.01"
                           min="0"
                           value="{{ old('descuento', '0') }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('descuento') border-red-500 @enderror">
                    @error('descuento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="space-y-6" id="subcategoriasSection" style="display: none;">
            <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">Características del Producto</h3>
            
            <div id="subcategoriasContainer" class="space-y-4">
            </div>
        </div>

        <div class="space-y-6">
            <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">Imagen Principal</h3>
            
            <div>
                <label for="image" class="block text-sm font-medium text-slate-700 mb-2">Imagen</label>
                <input type="file" 
                       name="image" 
                       id="image" 
                       accept="image/*"
                       class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('image') border-red-500 @enderror"
                       onchange="previewMainImage(event)">
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div id="mainImagePreview" class="mt-4 hidden">
                    <img src="" alt="Preview" class="w-32 h-32 object-cover rounded-md shadow-sm">
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">Galería de Imágenes</h3>
            
            <div>
                <label for="gallery" class="block text-sm font-medium text-slate-700 mb-2">Imágenes de Galería</label>
                <input type="file" 
                       name="gallery[]" 
                       id="gallery" 
                       accept="image/*"
                       multiple
                       class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('gallery.*') border-red-500 @enderror"
                       onchange="previewGallery(event)">
                <p class="mt-1 text-xs text-slate-500">Puedes seleccionar múltiples imágenes</p>
                @error('gallery.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div id="galleryPreview" class="mt-4 grid grid-cols-4 gap-4 hidden">
                </div>
            </div>
        </div>

        <div class="flex gap-4 pt-6 border-t">
            <button type="submit" 
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                Crear Producto
            </button>
            <a href="{{ route('productos.index') }}" 
               class="px-6 py-2.5 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>

<script>
// Búsqueda de marcas
document.getElementById('searchMarcas').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const marcasItems = document.querySelectorAll('.marca-item');
    const noResultsMsg = document.getElementById('noMarcasFound');
    let visibleCount = 0;
    
    marcasItems.forEach(item => {
        const title = item.querySelector('.marca-title').textContent.toLowerCase();
        if (title.includes(searchTerm)) {
            item.style.display = 'flex';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    noResultsMsg.classList.toggle('hidden', visibleCount > 0);
});

// Búsqueda de modelos
document.getElementById('searchModelos').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const modelosItems = document.querySelectorAll('.modelo-item');
    const noResultsMsg = document.getElementById('noModelosFound');
    let visibleCount = 0;
    
    modelosItems.forEach(item => {
        const title = item.querySelector('.modelo-title').textContent.toLowerCase();
        if (title.includes(searchTerm)) {
            item.style.display = 'flex';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    noResultsMsg.classList.toggle('hidden', visibleCount > 0);
});

// Contador de marcas seleccionadas
function updateMarcasCount() {
    const checked = document.querySelectorAll('input[name="marcas[]"]:checked').length;
    const counter = document.getElementById('selectedMarcasCount');
    if (checked > 0) {
        counter.textContent = `${checked} marca${checked !== 1 ? 's' : ''} seleccionada${checked !== 1 ? 's' : ''}`;
        counter.classList.remove('hidden');
    } else {
        counter.textContent = '';
    }
}

// Contador de modelos seleccionados
function updateModelosCount() {
    const checked = document.querySelectorAll('input[name="modelos[]"]:checked').length;
    const counter = document.getElementById('selectedModelosCount');
    if (checked > 0) {
        counter.textContent = `${checked} modelo${checked !== 1 ? 's' : ''} seleccionado${checked !== 1 ? 's' : ''}`;
        counter.classList.remove('hidden');
    } else {
        counter.textContent = '';
    }
}

// Event listeners para los contadores
document.querySelectorAll('input[name="marcas[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', updateMarcasCount);
});

document.querySelectorAll('input[name="modelos[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', updateModelosCount);
});

// Inicializar contadores
updateMarcasCount();
updateModelosCount();

document.getElementById('categoria_id').addEventListener('change', function() {
    const categoriaId = this.value;
    const subcategoriasSection = document.getElementById('subcategoriasSection');
    const subcategoriasContainer = document.getElementById('subcategoriasContainer');
    
    subcategoriasContainer.innerHTML = '';
    subcategoriasSection.style.display = 'none';
    
    if (categoriaId) {
        fetch(`/admin/productos/subcategorias/${categoriaId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    subcategoriasSection.style.display = 'block';
                    
                    data.forEach(subcategoria => {
                        const html = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border border-slate-200 rounded-md bg-slate-50">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">
                                        ${subcategoria.title}
                                        ${subcategoria.title2 ? '<span class="text-slate-500 text-xs"> (' + subcategoria.title2 + ')</span>' : ''}
                                    </label>
                                </div>
                                <div>
                                    <input type="hidden" name="subcategorias[${subcategoria.id}][id]" value="${subcategoria.id}">
                                    <input type="text" 
                                           name="subcategorias[${subcategoria.id}][valor]" 
                                           placeholder="Ingrese valor"
                                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                                </div>
                            </div>
                        `;
                        subcategoriasContainer.insertAdjacentHTML('beforeend', html);
                    });
                }
            })
            .catch(error => console.error('Error:', error));
    }
});

function previewMainImage(event) {
    const preview = document.getElementById('mainImagePreview');
    const img = preview.querySelector('img');
    
    if (event.target.files && event.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(event.target.files[0]);
    }
}

function previewGallery(event) {
    const preview = document.getElementById('galleryPreview');
    preview.innerHTML = '';
    
    if (event.target.files && event.target.files.length > 0) {
        preview.classList.remove('hidden');
        
        Array.from(event.target.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-24 object-cover rounded-md shadow-sm">
                `;
                preview.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    } else {
        preview.classList.add('hidden');
    }
}
</script>
@endsection