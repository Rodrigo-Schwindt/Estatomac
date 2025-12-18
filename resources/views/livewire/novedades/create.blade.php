@extends('layouts.admin')

@section('content')

<div class="mx-auto space-y-8 animate-fadeIn">

    <div class="flex justify-between items-center pb-4">
        <h2 class="text-2xl font-bold text-slate-900">Crear Nueva Novedad</h2>

        <a href="{{ route('novedades.index') }}"
           class="px-4 py-2 border border-slate-300 rounded-md hover:bg-slate-100 transition">
            ← Volver
        </a>
    </div>

    <form action="{{ route('novedades.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white border border-slate-200 rounded-md p-6 space-y-6 shadow-sm">

        @csrf

        <div>
            <label class="block mb-1 text-sm font-medium text-slate-800">Título *</label>
            <input type="text" name="title"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-600 focus:outline-none"
                   required>
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium text-slate-800">Descripción</label>
            <textarea name="description"
                     id="description"
                      class="w-full border border-slate-300 rounded-md px-3 py-2 h-28 resize-none focus:ring-2 focus:ring-blue-600 focus:outline-none"></textarea>
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium text-slate-800">Orden</label>
            <input type="text" name="orden"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 font-mono uppercase focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="destacado"
                   class="w-4 h-4 text-blue-600 border-slate-300 rounded">
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
                    class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
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
            <label class="block mb-1 text-sm font-medium text-slate-800">Imagen Principal</label>

            <img id="preview-image" class="hidden w-32 h-32 object-cover rounded-md border border-slate-200 mb-2">

            <input type="file" name="image" required class="hidden" id="img1" accept="image/*">

            <button type="button"
                    onclick="document.getElementById('img1').click()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer">
                Subir Imagen
            </button>
                                   <p class="text-xs text-slate-500 leading-relaxed">
             Recomendado 800×800px • JPG / PNG / WEBP • Máx 3MBa.
    </p>
        </div>


        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-200">
            <a href="{{ route('novedades.index') }}"
               class="px-6 py-2 border border-slate-300 rounded-md hover:bg-slate-100 transition">
                Cancelar
            </a>

            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                Crear Novedad
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

    document.getElementById('img1').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const preview = document.getElementById('preview-image');
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
    });

    document.getElementById('img2').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        const preview = document.getElementById('preview-banner');
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
    });

});
</script>

@endsection
