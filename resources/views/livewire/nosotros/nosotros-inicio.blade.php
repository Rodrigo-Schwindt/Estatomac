@extends('layouts.admin')
@php
$previews = [
    'image_home' => $nosotros && $nosotros->image_home ? asset('storage/' . $nosotros->image_home) : null,
];
@endphp

@section('content')

<section class="space-y-6 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">

    <h3 class="text-[24px] font-semibold text-slate-900">Nosotros Inicio</h3>
    <p class="text-sm text-slate-600 italic">
        Contenido mostrado en la sección introductoria de "Nosotros".
    </p>

    <form method="POST"
          action="{{ route('nosotros.home.save') }}"
          enctype="multipart/form-data"
          class="space-y-6">

        @csrf

        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-800">Título Inicio</label>
            <input type="text" name="title_home"
                   value="{{ old('title_home', $nosotros->title_home ?? '') }}"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
        </div>

        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-800">Descripción Inicio</label>
            <textarea name="description_home"
                        id="description"
                      class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm h-32 bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">{{ old('description_home', $nosotros->description_home ?? '') }}</textarea>
        </div>

        <div class="space-y-3">
            <h4 class="text-lg font-semibold text-slate-900">Imagen Inicio</h4>
            <p class="text-xs text-slate-500 leading-relaxed">
                La imagen tiene que tener fondo transparente o negro*
       </p>
            @if($previews['image_home'])
                <img id="preview-image_home"
                     src="{{ $previews['image_home'] }}"
                     class="w-[600px] max-h-64 object-cover rounded-md border border-slate-200">
            @else
                <div id="placeholder-image_home"
                     class="h-32 w-full max-w-xs bg-slate-100 border-2 border-dashed border-slate-300
                     rounded-md flex items-center justify-center text-slate-500 text-sm">
                     Sin imagen
                </div>

                <img id="preview-image_home"
                     class="hidden w-[600px] max-h-64 object-cover rounded-md border border-slate-200">
            @endif
        </div>

        <div class="flex flex-wrap gap-2">
            <input id="file-image_home"
                   type="file"
                   name="image_home"
                   accept="image/*"
                   class="hidden">

            <button type="button"
                    onclick="document.getElementById('file-image_home').click()"
                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition cursor-pointer">
                {{ $previews['image_home'] ? 'Cambiar Imagen' : 'Subir Imagen' }}
            </button>

            @if($previews['image_home'])
                <button type="button"
                        onclick="deleteImageHome()"
                        class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition cursor-pointer">
                    Eliminar
                </button>
            @endif
        </div>
                       <p class="text-xs text-slate-500 leading-relaxed">
             Recomendado 600x600px  JPG / PNG / WEBP, Max 3MB.
    </p>

        <div class="pt-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-md 
                           hover:bg-blue-700 transition active:scale-[.98] cursor-pointer">
                Guardar Cambios
            </button>
        </div>

    </form>

</section>


<script>
            let editorDescription;

    ClassicEditor
        .create(document.querySelector('#description'))
        .then(e => editorDescription = e)
        .catch(error => console.error(error));
    document.getElementById('file-image_home')?.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const url = URL.createObjectURL(file);

        const img = document.getElementById('preview-image_home');
        img.src = url;
        img.classList.remove('hidden');

        const placeholder = document.getElementById('placeholder-image_home');
        placeholder?.classList.add('hidden');
    });

    function deleteImageHome() {
        if (!confirm("¿Eliminar la imagen?")) return;

        fetch("{{ route('nosotros.home.image.delete') }}", {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) return alert("Error eliminando la imagen");
            window.location.reload();
        });
    }
</script>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>

@endsection
