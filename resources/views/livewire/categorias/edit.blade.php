@extends('layouts.admin')

@section('content')
<div class=" mx-auto space-y-8 animate-fadeIn">
    <form id="form-categoria" action="{{ route('categorias.update', $categoria) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-6">
        @csrf
        @method('PUT')
        
        <input type="hidden" name="deleted_subcategorias[]" id="deletedSubcategorias">
        
        <div class="space-y-6">
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

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="col-span-full">
                    <label class="block text-sm font-medium text-slate-700 mb-3">
                        Categorías Padre (Selecciona todas las que correspondan)
                    </label>
                    
                    <div class="flex gap-6 max-h-64 overflow-y-auto p-1 custom-scrollbar">
                        @foreach($categoriasPadre as $categoriaPadre)
                            @php
                                $checked = (is_array(old('categoria_padre_id')) && in_array($categoriaPadre->id, old('categoria_padre_id'))) 
                                           || (isset($categoria) && $categoria->categoriasPadre->contains($categoriaPadre->id));
                            @endphp
                            
                            <label class="relative w-87 flex items-center p-4 cursor-pointer rounded-xl border-2 transition-all hover:bg-slate-50 group {{ $checked ? 'border-blue-600 bg-blue-50' : 'border-slate-200' }}">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" 
                                           name="categoria_padre_id[]" 
                                           value="{{ $categoriaPadre->id }}"
                                           class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer"
                                           {{ $checked ? 'checked' : '' }}
                                           onchange="updateCheckboxStyle(this)">
                                </div>
                                <div class="ml-3 text-sm">
                                    <span class="font-medium {{ $checked ? 'text-blue-900' : 'text-slate-700' }} group-hover:text-blue-600 transition-colors">
                                        {{ $categoriaPadre->title }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    
                    @error('categoria_padre_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

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
        <div class="flex items-center gap-2 mt-4">
            <input type="hidden" name="sin_descuentos" value="0">
            <input type="checkbox" name="sin_descuentos" id="sin_descuentos" value="1" {{ old('sin_descuentos', $categoria->sin_descuentos ?? false) ? 'checked' : '' }} class="rounded border-slate-300">
            <label for="sin_descuentos" class="text-sm font-medium text-slate-700">Esta categoría no permite descuentos adicionales (Cliente/Pago)</label>
        </div>

        <div class="space-y-6">
            <div class="flex items-center justify-between border-b pb-2">
                <h3 class="text-lg font-semibold text-slate-900">Subcategorías</h3>
                <button type="button" 
                        onclick="addSubcategoria()"
                        class="inline-flex cursor-pointer items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm">
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
                                <select name="subcategorias[{{ $index }}][tolerancia]" 
                                        class="w-full px-4 py-2 border cursor-pointer border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600"
                                        required>
                                    <option value="0" {{ $subcategoria->tolerancia == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $subcategoria->tolerancia == 1 ? 'selected' : '' }}>Sí</option>
                                </select>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex gap-4 pt-6 ">
            <button type="submit" 
                    class="px-6 py-2.5 cursor-pointer bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
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
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Título <span class="text-red-500">*</span></label>
                    <input type="text" name="subcategorias[${index}][title]" class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
                    <input type="text" name="subcategorias[${index}][order]" class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tolerancia <span class="text-red-500">*</span></label>
                    <select name="subcategorias[${index}][tolerancia]" class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
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
    const form = document.getElementById('form-categoria');
    if (!form) return;

    form.querySelectorAll('input[name="deleted_subcategorias[]"]').forEach(input => input.remove());
    
    deletedSubcategorias.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'deleted_subcategorias[]';
        input.value = id;
        form.appendChild(input);
    });
}
</script>
<script>
function updateCheckboxStyle(checkbox) {
    const parentLabel = checkbox.closest('label');
    const textSpan = parentLabel.querySelector('span');
    
    if (checkbox.checked) {
        parentLabel.classList.remove('border-slate-200');
        parentLabel.classList.add('border-blue-600', 'bg-blue-50');
        textSpan.classList.add('text-blue-900');
    } else {
        parentLabel.classList.remove('border-blue-600', 'bg-blue-50');
        parentLabel.classList.add('border-slate-200');
        textSpan.classList.remove('text-blue-900');
    }
}
</script>
<style>
    /* Checkbox personalizado más grande y estético */
    input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;
        background-color: #fff;
        margin: 0;
        font: inherit;
        color: currentColor;
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid #cbd5e1;
        border-radius: 0.35rem;
        display: grid;
        place-content: center;
        transition: all 0.2s ease-in-out;
    }
    
    input[type="checkbox"]::before {
        content: "";
        width: 0.65em;
        height: 0.65em;
        transform: scale(0);
        transition: 120ms transform ease-in-out;
        box-shadow: inset 1em 1em #2563eb; /* Azul Blue-600 */
        clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
    }
    
    input[type="checkbox"]:checked {
        border-color: #2563eb;
        background-color: #eff6ff;
    }
    
    input[type="checkbox"]:checked::before {
        transform: scale(1);
    }
    
    /* Scrollbar estética para la lista */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    </style>
@endsection