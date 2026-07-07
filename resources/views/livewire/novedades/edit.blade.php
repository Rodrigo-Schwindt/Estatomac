@extends('layouts.admin')

@section('content')

<div class="mx-auto space-y-8 animate-fadeIn">

    <div class="flex justify-between items-center pb-4">
        <h2 class="text-2xl font-bold text-slate-900">Editar Novedad</h2>

        <a href="{{ route('novedades.index') }}"
           class="px-4 py-2 border border-slate-300 rounded-md hover:bg-slate-100 transition">
            ← Volver
        </a>
    </div>

    <form action="{{ route('novedades.update', $novedad->id) }}"
          method="POST"
          enctype="multipart/form-data"
          class="bg-white border border-slate-200 rounded-md p-6 space-y-6 shadow-sm">
        
        @csrf
        @method('PUT') 

        <div>
            <label class="block mb-1 text-sm font-medium text-slate-800">Título *</label>
            <input type="text" name="title" value="{{ $novedad->title }}"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium text-slate-800">Descripción</label>
            <textarea name="description"
                        id="description"
                      class="w-full border border-slate-300 rounded-md px-3 py-2 h-28 resize-none focus:ring-2 focus:ring-blue-600 focus:outline-none">{{ $novedad->description }}</textarea>
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium text-slate-800">Orden</label>
            <input type="text" name="orden" value="{{ $novedad->orden }}"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 uppercase font-mono focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="destacado" value="1" {{ $novedad->destacado ? 'checked' : '' }}
                   class="w-4 h-4 text-blue-600 border-slate-300 rounded cursor-pointer">
            <label class="text-sm font-medium text-slate-800">Destacado</label>
        </div>

        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-800">Categorías*</label>

            <div class="border border-slate-300 rounded-md bg-white max-h-48 overflow-y-auto p-2 space-y-1 mt-2">

                @foreach($categories as $cat)
                    <label class="flex items-center gap-2 px-2 py-1 cursor-pointer hover:bg-slate-50 rounded">

                        <input 
                            type="checkbox"
                            name="selectedCategories[]" 
                            value="{{ $cat->id }}"
                            class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer"

                            {{ in_array(
                                $cat->id,
                                old('selectedCategories', $novedad->novcategories->pluck('id')->toArray())
                            ) ? 'checked' : '' }}
                        >

                        <span class="text-sm text-slate-700">
                            {{ $cat->title }}
                        </span>

                    </label>
                @endforeach

            </div>

            @error('selectedCategories')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-800 mb-2">Imagen</label>

            <input type="file" name="newImage" id="newImg" class="hidden" accept="image/*" onchange="previewNewImage(event)">

            <button type="button"
                    onclick="document.getElementById('newImg').click()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer mb-3">
                Subir Nueva Imagen
            </button>

            <p class="text-xs text-slate-500 mb-3">
                Recomendado: 800x800px o proporción 1:1
            </p>

            <div id="imageContainer">
                @if($novedad->image)
                    <div id="imageCurrent">
                        <p class="text-xs text-slate-600 mb-2">Imagen actual:</p>
                        <img src="{{ Storage::url($novedad->image) }}"
                             class="w-64 h-64 object-cover rounded-md shadow-sm border border-slate-200">
                    </div>
                @else
                    <div id="imagePlaceholder" class="w-64 h-64 border-2 border-dashed border-slate-300 rounded-md flex items-center justify-center bg-slate-50">
                        <div class="text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm">Vista previa</p>
                        </div>
                    </div>
                @endif
                <div id="imagePreview" class="hidden mt-4">
                    <p class="text-xs text-slate-600 mb-2">Nueva imagen:</p>
                    <img id="preview-newImage" src="" alt="Preview" class="w-64 h-64 object-cover rounded-md shadow-sm border border-slate-200">
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-200">
            <a href="{{ route('novedades.index') }}"
               class="px-6 py-2 border border-slate-300 rounded-md hover:bg-slate-100 transition">
                Cancelar
            </a>

            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer">
                Guardar Cambios
            </button>
        </div>
    </form>

</div>

<style>
@keyframes fadeIn { from {opacity:0; transform:translateY(6px)} to {opacity:1; transform:translateY(0)} }
.animate-fadeIn { animation: fadeIn .28s ease; }
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    let editorDescription;

    ClassicEditor
        .create(document.querySelector('#description'))
        .then(e => editorDescription = e)
        .catch(error => console.error(error));
});

function previewNewImage(event) {
    const current = document.getElementById('imageCurrent');
    const placeholder = document.getElementById('imagePlaceholder');
    const preview = document.getElementById('imagePreview');
    const img = document.getElementById('preview-newImage');
    
    if (event.target.files && event.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            if (current) current.classList.add('hidden');
            if (placeholder) placeholder.classList.add('hidden');
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(event.target.files[0]);
    }
}
</script>

@endsection