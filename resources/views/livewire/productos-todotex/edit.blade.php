@extends('layouts.admin')

@section('content')
<div class="mx-auto space-y-8 animate-fadeIn">

    <div id="alertContainer"></div>

    <form id="productoForm" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-6">
        @csrf
        <input type="hidden" name="back_page" value="{{ $backPage }}">

        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-slate-900">Editar Producto #{{ $producto->id }}</h2>
            <a href="{{ route('productos-todotex.index') }}?page={{ $backPage }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>

        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">Contenido</h3>

            <div>
                <label for="descripcion" class="block text-sm font-medium text-slate-700 mb-2">Título / Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="4"
                          class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600"
                          placeholder="Título y descripción del producto...">{{ $producto->descripcion }}</textarea>
                <p id="descripcion-error" class="mt-1 text-sm text-red-600 hidden"></p>
            </div>

            <div>
                <label for="presentacion" class="block text-sm font-medium text-slate-700 mb-2">Presentación</label>
                <textarea name="presentacion" id="presentacion" rows="3"
                          class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600"
                          placeholder="Presentación del producto...">{{ $producto->presentacion }}</textarea>
                <p id="presentacion-error" class="mt-1 text-sm text-red-600 hidden"></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        PK Producto <span class="text-slate-400 font-normal text-xs">(PK del ERP, solo lectura)</span>
                    </label>
                    <input type="text"
                           value="{{ $producto->pk_externa ?? '—' }}"
                           readonly disabled
                           class="w-full px-4 py-2 border border-slate-300 rounded-md bg-slate-100 text-slate-700 font-mono cursor-not-allowed">
                </div>

                <div>
                    <label for="codigo" class="block text-sm font-medium text-slate-700 mb-2">Código</label>
                    <input type="text"
                           name="codigo"
                           id="codigo"
                           value="{{ $producto->codigo }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <p id="codigo-error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>

                <div>
                    <label for="color_todotex_id" class="block text-sm font-medium text-slate-700 mb-2">Color</label>
                    <select name="color_todotex_id"
                            id="color_todotex_id"
                            class="w-full px-4 py-2 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="">Sin color</option>
                        @foreach($colores as $color)
                            <option value="{{ $color->id }}" {{ (int) $producto->color_todotex_id === (int) $color->id ? 'selected' : '' }}>
                                {{ $color->codigo_color }} ({{ $color->titulo }})
                            </option>
                        @endforeach
                    </select>
                    <p id="color_todotex_id-error" class="mt-1 text-sm text-red-600 hidden"></p>
                    <p class="mt-1 text-xs text-slate-500">Al seleccionar un color se guardan autom&aacute;ticamente el c&oacute;digo y el nombre del color.</p>
                </div>

                <div>
                    <label for="orden" class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
                    <input type="text"
                           name="orden"
                           id="orden"
                           value="{{ $producto->orden }}"
                           placeholder="Ej: AA, AB, AAA..."
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 font-mono uppercase">
                    <p id="orden-error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>

                
                <div class="flex gap-6 items-center xl:col-span-3">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="destacado" name="destacado" value="1"
                               {{ $producto->destacado ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-2 focus:ring-blue-600">
                        <label for="destacado" class="text-sm font-medium text-slate-700 cursor-pointer">Destacado</label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="visible" name="visible" value="1"
                               {{ $producto->visible ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-2 focus:ring-blue-600">
                        <label for="visible" class="text-sm font-medium text-slate-700 cursor-pointer">Visible</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Precios -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">Precios</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="bulto" class="block text-sm font-medium text-slate-700 mb-2">Bulto</label>
                    <input type="text"
                           name="bulto"
                           id="bulto"
                           value="{{ $producto->bulto }}"
                           placeholder="Ej: 12 unidades"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <p id="bulto-error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>

                <div>
                    <label for="bulto_cantidad" class="block text-sm font-medium text-slate-700 mb-2">Bulto Cantidad</label>
                    <input type="number"
                           name="bulto_cantidad"
                           id="bulto_cantidad"
                           value="{{ $producto->bulto_cantidad }}"
                           min="1"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <p id="bulto_cantidad-error" class="mt-1 text-sm text-red-600 hidden"></p>
                    <p class="mt-1 text-xs text-slate-500">Pod&eacute;s editarlo manualmente.</p>
                </div>


                <div>
                    <label for="precio_unitario" class="block text-sm font-medium text-slate-700 mb-2">Precio Unitario</label>
                    <input type="number" name="precio_unitario" id="precio_unitario" step="0.01" min="0"
                           value="{{ $producto->precio_unitario }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <p id="precio_unitario-error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>

                

                <div>
                    <label for="descuento" class="block text-sm font-medium text-slate-700 mb-2">Descuento (%)</label>
                    <input type="number" name="descuento" id="descuento" step="0.01" min="0"
                           value="{{ $producto->descuento }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <p id="descuento-error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
            </div>
        </div>

        <!-- Categorías -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">Categorías</h3>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-3">Asignar Categorías</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                    <input type="text"
                           id="searchCategorias"
                           placeholder="Buscar categorías..."
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 md:col-span-3 lg:col-span-1">

                    <select id="familiaFilterCategorias"
                            class="w-full px-4 py-2 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="">Todas las familias</option>
                        @foreach($familias as $familia)
                            <option value="{{ $familia->id }}">{{ $familia->titulo }}</option>
                        @endforeach
                    </select>

                    <select id="rubroFilterCategorias"
                            class="w-full px-4 py-2 border border-slate-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="">Todos los rubros</option>
                        <option value="sin_rubro">Sin rubro</option>
                        @foreach($rubros as $rubro)
                            <option value="{{ $rubro->id }}">{{ $rubro->titulo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-between gap-3 mb-3">
                    <p class="text-xs text-slate-500">Podés combinar búsqueda con filtros por familia y rubro.</p>
                    <button type="button"
                            id="clearCategoriasFilters"
                            class="text-xs font-medium text-blue-600 hover:text-blue-700 cursor-pointer">
                        Limpiar filtros
                    </button>
                </div>

                <div class="border border-slate-300 rounded-md p-4 max-h-60 overflow-y-auto bg-slate-50">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3" id="categoriasContainer">
                        @foreach($categorias as $cat)
                            <label class="flex items-center gap-2 p-2 rounded-md hover:bg-white cursor-pointer transition categoria-item"
                                   data-familia-id="{{ $cat->familia_id }}"
                                   data-rubro-id="{{ $cat->rubro_id ?? '' }}">
                                <input type="checkbox"
                                       name="categorias[]"
                                       value="{{ $cat->id }}"
                                       {{ $producto->categorias->contains($cat->id) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-2 focus:ring-blue-600">
                                <div>
                                    <span class="text-sm text-slate-700 categoria-titulo block">{{ $cat->titulo }}</span>
                                    @if($cat->familia)
                                        <span class="text-xs text-slate-400">{{ $cat->familia->titulo }}</span>
                                    @endif
                                    @if($cat->rubro)
                                        <span class="text-xs text-emerald-600 block">{{ $cat->rubro->titulo }}</span>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <div id="noCategoriasFound" class="hidden text-center text-slate-500 text-sm py-4">
                        No se encontraron categorías
                    </div>
                </div>
                <div id="selectedCategoriasCount" class="mt-2 text-xs text-slate-600"></div>
                <p id="categorias-error" class="mt-1 text-sm text-red-600 hidden"></p>
            </div>
        </div>

        <!-- Galería -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">Galería de Imágenes</h3>

            <p class="text-xs text-slate-500">Hacé clic en la estrella ★ para marcar una imagen como portada (imagen principal).</p>

            @if($producto->gallery->count() > 0)
                <div>
                    <p class="text-sm font-medium text-slate-700 mb-3">Imágenes actuales:</p>
                    <div id="existingGallery" class="flex flex-wrap gap-4 mb-4">
                        @foreach($producto->gallery as $image)
                            <div class="relative group gallery-item" data-id="{{ $image->id }}">
                                <div class="w-40 h-40 relative">
                                    <img src="{{ Storage::url($image->image) }}"
                                         alt="Gallery"
                                         class="w-full h-full object-cover rounded-md shadow-sm border-2 {{ $image->portada ? 'border-yellow-400' : 'border-slate-200' }}">

                                    <!-- Portada button -->
                                    <button type="button"
                                            onclick="setPortadaExistente({{ $image->id }}, this)"
                                            class="absolute top-1 left-1 rounded-full p-1 shadow-lg cursor-pointer transition-all {{ $image->portada ? 'bg-yellow-400 text-white' : 'bg-white/80 text-slate-400 opacity-0 group-hover:opacity-100' }} hover:bg-yellow-400 hover:text-white"
                                            title="Marcar como portada">
                                        ★
                                    </button>

                                    <!-- Delete button -->
                                    <button type="button"
                                            onclick="deleteExistingImage({{ $producto->id }}, {{ $image->id }}, this)"
                                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600 shadow-lg cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>

                                    @if($image->portada)
                                        <span class="absolute bottom-1 left-1 bg-yellow-400 text-white text-xs px-1.5 py-0.5 rounded font-medium">Portada</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="portada_id" id="portadaIdInput" value="{{ $producto->gallery->where('portada', true)->first()?->id ?? '' }}">
                </div>
            @endif

            <div>
                <label for="images" class="block text-sm font-medium text-slate-700 mb-2">Agregar Imágenes</label>
                <input type="file"
                       name="images[]"
                       id="images"
                       accept="image/*"
                       multiple
                       class="w-full px-4 py-2 cursor-pointer border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600"
                       onchange="addGalleryImages(event)">
                <p class="mt-1 text-xs text-slate-500">Puedes seleccionar múltiples imágenes. Recomendado: 800x800px</p>
                <p id="images-error" class="mt-1 text-sm text-red-600 hidden"></p>

                <div id="galleryNewContainer" class="mt-4">
                    <div id="galleryNewPlaceholder" class="w-full h-24 border-2 border-dashed border-slate-300 rounded-md flex items-center justify-center bg-slate-50">
                        <div class="text-center text-slate-400">
                            <p class="text-sm">Nuevas imágenes aparecerán aquí</p>
                        </div>
                    </div>
                    <div id="galleryNewPreview" class="hidden flex flex-wrap gap-4 mt-2"></div>
                </div>
                <input type="hidden" name="portada_index" id="portadaIndexInput" value="">
            </div>
        </div>

        <div class="flex gap-4 pt-6 border-t border-slate-200">
            <button type="submit"
                    id="submitBtn"
                    class="px-6 py-2.5 cursor-pointer bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="submitText">Actualizar Producto</span>
                <span id="submitLoading" class="hidden items-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Actualizando...
                </span>
            </button>
            <a href="{{ route('productos-todotex.index') }}"
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
let galleryFiles = [];
let newPortadaIndex = null;

function filterCategorias() {
    const searchTerm = document.getElementById('searchCategorias').value.toLowerCase().trim();
    const familiaValue = document.getElementById('familiaFilterCategorias').value;
    const rubroValue = document.getElementById('rubroFilterCategorias').value;
    const items = document.querySelectorAll('.categoria-item');
    const noResultsMsg = document.getElementById('noCategoriasFound');
    let visibleCount = 0;

    items.forEach(item => {
        const searchableText = item.textContent.toLowerCase();
        const familiaId = item.dataset.familiaId || '';
        const rubroId = item.dataset.rubroId || '';

        const matchesSearch = !searchTerm || searchableText.includes(searchTerm);
        const matchesFamilia = !familiaValue || familiaId === familiaValue;
        const matchesRubro = !rubroValue
            || (rubroValue === 'sin_rubro' ? rubroId === '' : rubroId === rubroValue);

        if (matchesSearch && matchesFamilia && matchesRubro) {
            item.style.display = 'flex';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    noResultsMsg.classList.toggle('hidden', visibleCount > 0);
}

function updateCategoriasCount() {
    const checked = document.querySelectorAll('input[name="categorias[]"]:checked').length;
    const counter = document.getElementById('selectedCategoriasCount');
    counter.textContent = checked > 0 ? `${checked} categoría${checked !== 1 ? 's' : ''} seleccionada${checked !== 1 ? 's' : ''}` : '';
}
document.querySelectorAll('input[name="categorias[]"]').forEach(cb => cb.addEventListener('change', updateCategoriasCount));
updateCategoriasCount();

// Portada existente
function setPortadaExistente(imageId, btn) {
    // Reset all existing portada buttons
    document.querySelectorAll('#existingGallery .gallery-item').forEach(item => {
        const starBtn = item.querySelector('button[title="Marcar como portada"]');
        const img = item.querySelector('img');
        const badge = item.querySelector('span.bg-yellow-400');
        starBtn.classList.remove('bg-yellow-400', 'text-white');
        starBtn.classList.add('bg-white/80', 'text-slate-400', 'opacity-0', 'group-hover:opacity-100');
        img.classList.remove('border-yellow-400');
        img.classList.add('border-slate-200');
        if (badge) badge.remove();
    });
    // Reset new portada selection
    newPortadaIndex = null;
    document.getElementById('portadaIndexInput').value = '';
    renderGalleryPreviews();

    // Set selected
    btn.classList.add('bg-yellow-400', 'text-white');
    btn.classList.remove('bg-white/80', 'text-slate-400', 'opacity-0', 'group-hover:opacity-100');
    const img = btn.closest('.gallery-item').querySelector('img');
    img.classList.add('border-yellow-400');
    img.classList.remove('border-slate-200');

    // Add badge
    const badge = document.createElement('span');
    badge.className = 'absolute bottom-1 left-1 bg-yellow-400 text-white text-xs px-1.5 py-0.5 rounded font-medium';
    badge.textContent = 'Portada';
    btn.closest('.w-40').appendChild(badge);

    document.getElementById('portadaIdInput').value = imageId;
}

// Portada nueva
function setPortadaNueva(index) {
    newPortadaIndex = index;
    document.getElementById('portadaIndexInput').value = index;
    document.getElementById('portadaIdInput').value = '';

    // Clear existing portada highlights
    document.querySelectorAll('#existingGallery .gallery-item').forEach(item => {
        const starBtn = item.querySelector('button[title="Marcar como portada"]');
        const img = item.querySelector('img');
        const badge = item.querySelector('span.bg-yellow-400');
        if (starBtn) {
            starBtn.classList.remove('bg-yellow-400', 'text-white');
            starBtn.classList.add('bg-white/80', 'text-slate-400', 'opacity-0', 'group-hover:opacity-100');
        }
        if (img) { img.classList.remove('border-yellow-400'); img.classList.add('border-slate-200'); }
        if (badge) badge.remove();
    });

    renderGalleryPreviews();
}

function addGalleryImages(event) {
    const placeholder = document.getElementById('galleryNewPlaceholder');
    const preview = document.getElementById('galleryNewPreview');
    if (event.target.files && event.target.files.length > 0) {
        galleryFiles = galleryFiles.concat(Array.from(event.target.files));
        renderGalleryPreviews();
        placeholder.classList.add('hidden');
        preview.classList.remove('hidden');
        preview.classList.add('flex');
        event.target.value = '';
    }
}

function renderGalleryPreviews() {
    const preview = document.getElementById('galleryNewPreview');
    preview.innerHTML = '';
    galleryFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const isPortada = newPortadaIndex === index;
            const div = document.createElement('div');
            div.className = 'relative group';
            div.innerHTML = `
                <div class="w-40 h-40 relative">
                    <img src="${e.target.result}" class="w-full h-full object-cover rounded-md shadow-sm border-2 ${isPortada ? 'border-yellow-400' : 'border-slate-200'}">
                    <button type="button"
                            onclick="setPortadaNueva(${index})"
                            class="absolute top-1 left-1 rounded-full p-1 shadow-lg cursor-pointer transition-all ${isPortada ? 'bg-yellow-400 text-white' : 'bg-white/80 text-slate-400 opacity-0 group-hover:opacity-100'} hover:bg-yellow-400 hover:text-white"
                            title="Marcar como portada">★</button>
                    <button type="button"
                            onclick="removeNewGalleryImage(${index})"
                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600 shadow-lg cursor-pointer">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    ${isPortada ? '<span class="absolute bottom-1 left-1 bg-yellow-400 text-white text-xs px-1.5 py-0.5 rounded font-medium">Portada</span>' : ''}
                </div>
                <p class="text-xs text-slate-500 mt-1 truncate w-40">${file.name}</p>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

function removeNewGalleryImage(index) {
    galleryFiles.splice(index, 1);
    if (newPortadaIndex === index) { newPortadaIndex = null; document.getElementById('portadaIndexInput').value = ''; }
    else if (newPortadaIndex > index) { newPortadaIndex--; document.getElementById('portadaIndexInput').value = newPortadaIndex; }
    if (galleryFiles.length === 0) {
        document.getElementById('galleryNewPlaceholder').classList.remove('hidden');
        document.getElementById('galleryNewPreview').classList.add('hidden');
        document.getElementById('galleryNewPreview').classList.remove('flex');
    } else {
        renderGalleryPreviews();
    }
    updateGalleryInput();
}

function updateGalleryInput() {
    const input = document.getElementById('images');
    const dataTransfer = new DataTransfer();
    galleryFiles.forEach(file => dataTransfer.items.add(file));
    input.files = dataTransfer.files;
}

function deleteExistingImage(productoId, imageId, btn) {
    if (!confirm('¿Estás seguro de eliminar esta imagen?')) return;
    fetch(`/admin/productos-todotex/${productoId}/gallery/${imageId}`, {
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
            const item = document.querySelector(`.gallery-item[data-id="${imageId}"]`);
            if (item) item.remove();
            showAlert(data.message, 'success');
        }
    })
    .catch(() => showAlert('Error al eliminar la imagen', 'error'));
}

document.addEventListener('DOMContentLoaded', function() {
    let editorDescripcion, editorPresentacion;
    const searchCategorias = document.getElementById('searchCategorias');
    const familiaFilterCategorias = document.getElementById('familiaFilterCategorias');
    const rubroFilterCategorias = document.getElementById('rubroFilterCategorias');
    const clearCategoriasFilters = document.getElementById('clearCategoriasFilters');

    searchCategorias.addEventListener('input', filterCategorias);
    searchCategorias.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') e.preventDefault();
    });
    familiaFilterCategorias.addEventListener('change', filterCategorias);
    rubroFilterCategorias.addEventListener('change', filterCategorias);
    clearCategoriasFilters.addEventListener('click', function() {
        searchCategorias.value = '';
        familiaFilterCategorias.value = '';
        rubroFilterCategorias.value = '';
        filterCategorias();
    });

    filterCategorias();

    ClassicEditor.create(document.querySelector('#descripcion'))
        .then(e => editorDescripcion = e)
        .catch(error => console.error(error));

    ClassicEditor.create(document.querySelector('#presentacion'))
        .then(e => editorPresentacion = e)
        .catch(error => console.error(error));

    const form = document.getElementById('productoForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoading = document.getElementById('submitLoading');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(form);
        if (editorDescripcion) formData.set('descripcion', editorDescripcion.getData());
        if (editorPresentacion) formData.set('presentacion', editorPresentacion.getData());

        galleryFiles.forEach((file, i) => formData.append(`images[${i}]`, file));

        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');
        submitLoading.classList.add('flex');

        fetch(`{{ route('productos-todotex.update', $producto->id) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => { window.location.href = data.redirect; }, 1000);
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const key = field.replace(/\./g, '_');
                        const errorEl = document.getElementById(`${key}-error`);
                        if (errorEl) {
                            errorEl.textContent = Array.isArray(data.errors[field]) ? data.errors[field][0] : data.errors[field];
                            errorEl.classList.remove('hidden');
                        }
                    });
                } else {
                    showAlert(data.message || 'Error al actualizar el producto', 'error');
                }
            }
        })
        .catch(error => { console.error('Error:', error); showAlert('Error al actualizar el producto', 'error'); })
        .finally(() => {
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            submitLoading.classList.add('hidden');
            submitLoading.classList.remove('flex');
        });
    });

    function clearErrors() {
        document.querySelectorAll('[id$="-error"]').forEach(el => { el.classList.add('hidden'); el.textContent = ''; });
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
