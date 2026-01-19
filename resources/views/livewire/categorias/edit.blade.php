@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fadeIn">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-slate-900">Editar Categoría</h2>
        <a href="{{ route('categorias.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <form action="{{ route('categorias.update', $categoria) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-6">
        @csrf
        @method('PUT')
        
        <input type="hidden" name="deleted_subcategorias[]" id="deletedSubcategorias">

        <div class="space-y-6">
            <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">Información de la Categoría</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-2">
                        Título <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $categoria->title) }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('title') border-red-500 @enderror"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="order" class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
                    <input type="text" 
                           name="order" 
                           id="order" 
                           value="{{ old('order', $categoria->order) }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('order') border-red-500 @enderror">
                    @error('order')
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
                        <option value="0" {{ old('destacado', $categoria->destacado) == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('destacado', $categoria->destacado) == '1' ? 'selected' : '' }}>Sí</option>
                    </select>
                    @error('destacado')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-slate-700 mb-2">Imagen</label>
                @if($categoria->image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $categoria->image) }}" 
                             alt="{{ $categoria->title }}" 
                             class="w-32 h-32 object-cover rounded-md shadow-sm">
                    </div>
                @endif
                <input type="file" 
                       name="image" 
                       id="image" 
                       accept="image/*"
                       class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('image') border-red-500 @enderror"
                       onchange="previewImage(event)">
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div id="imagePreview" class="mt-4 hidden">
                    <img src="" alt="Preview" class="w-32 h-32 object-cover rounded-md shadow-sm">
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="flex items-center justify-between border-b pb-2">
                <h3 class="text-lg font-semibold text-slate-900">Subcategorías</h3>
                <button type="button" 
                        onclick="addSubcategoria()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Agregar Subcategoría
                </button>
            </div>

            <div id="subcategoriasContainer" class="space-y-4">
                @foreach($categoria->subcategorias as $index => $subcategoria)
                    <div class="subcategoria-item p-4 border border-slate-200 rounded-md bg-slate-50 space-y-4" data-index="{{ $index }}" data-id="{{ $subcategoria->id }}">
                        <div class="flex items-center justify-between">
                            <h4 class="font-medium text-slate-700">Subcategoría #{{ $index + 1 }}</h4>
                            <button type="button" 
                                    onclick="removeSubcategoria({{ $index }}, {{ $subcategoria->id }})"
                                    class="text-red-600 hover:text-red-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                        
                        <input type="hidden" name="subcategorias[{{ $index }}][id]" value="{{ $subcategoria->id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Título <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="subcategorias[{{ $index }}][title]" 
                                       value="{{ old('subcategorias.'.$index.'.title', $subcategoria->title) }}"
                                       class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600"
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
                                <input type="text" 
                                       name="subcategorias[{{ $index }}][order]" 
                                       value="{{ old('subcategorias.'.$index.'.order', $subcategoria->order) }}"
                                       class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    Tolerancia <span class="text-red-500">*</span>
                                </label>
                                <select name="subcategorias[${index}][tolerancia]" 
                                        class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600"
                                        required>
                                    <option value="0" ${subcategoria.tolerancia == 0 ? 'selected' : ''}>No</option>
                                    <option value="1" ${subcategoria.tolerancia == 1 ? 'selected' : ''}>Sí</option>
                                </select>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex gap-4 pt-6 border-t">
            <button type="submit" 
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                Actualizar Categoría
            </button>
            <a href="{{ route('categorias.index') }}" 
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
let subcategoriaIndex = {{ $categoria->subcategorias->count() }};
let deletedSubcategorias = [];

function previewImage(event) {
    const preview = document.getElementById('imagePreview');
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

function addSubcategoria() {
    const container = document.getElementById('subcategoriasContainer');
    const index = subcategoriaIndex++;
    
    const html = `
        <div class="subcategoria-item p-4 border border-slate-200 rounded-md bg-slate-50 space-y-4" data-index="${index}">
            <div class="flex items-center justify-between">
                <h4 class="font-medium text-slate-700">Subcategoría #${index + 1}</h4>
                <button type="button" 
                        onclick="removeSubcategoria(${index})"
                        class="text-red-600 hover:text-red-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Título <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="subcategorias[${index}][title]" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
                    <input type="text" 
                           name="subcategorias[${index}][order]" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
}

function removeSubcategoria(index, id = null) {
    const item = document.querySelector(`.subcategoria-item[data-index="${index}"]`);
    if (item) {
        if (id) {
            deletedSubcategorias.push(id);
            updateDeletedInput();
        }
        item.remove();
    }
}

function updateDeletedInput() {
    const container = document.getElementById('deletedSubcategorias').parentElement;
    container.querySelectorAll('input[name="deleted_subcategorias[]"]').forEach(input => input.remove());
    
    deletedSubcategorias.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'deleted_subcategorias[]';
        input.value = id;
        container.appendChild(input);
    });
}
</script>
@endsection