@extends('layouts.admin')

@section('content')

@php
$previews = [
    'image' => $nosotros && $nosotros->image ? asset('storage/' . $nosotros->image) : null,
    'image_1' => $nosotros && $nosotros->image_1 ? asset('storage/' . $nosotros->image_1) : null,
    'image_2' => $nosotros && $nosotros->image_2 ? asset('storage/' . $nosotros->image_2) : null,
    'image_3' => $nosotros && $nosotros->image_3 ? asset('storage/' . $nosotros->image_3) : null,
    'image_4' => $nosotros && $nosotros->image_4 ? asset('storage/' . $nosotros->image_4) : null,
    'banner_image' => $banner && $banner->image_banner ? asset('storage/' . $banner->image_banner) : null,
];
@endphp

<div class="mx-auto py-10 animate-fadeIn space-y-10 bg-white border border-slate-200 rounded-md shadow-sm p-16 px-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-md px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md px-4 py-3">
            Hubo errores al guardar. Revisá los campos marcados.
        </div>
    @endif

    <form method="POST"
          action="{{ route('nosotros.save') }}"
          enctype="multipart/form-data"
          class="space-y-12">
        @csrf

        <section class="space-y-8">
            <h3 class="text-[24px] font-semibold text-slate-900">Banner</h3>
        
            <div>
                <label class="block text-sm font-medium text-slate-900 mb-1">Título del Banner</label>
                <input type="text"
                       name="banner_title"
                       value="{{ old('banner_title', $banner->title ?? '') }}"
                       class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>
        
            <div class="space-y-2">
                <h4 class="text-sm font-medium text-slate-900">Imagen del Banner</h4>
        
                @if($previews['banner_image'])
                    <img id="preview-banner_image"
                         src="{{ $previews['banner_image'] }}"
                         class="w-full max-h-64 object-cover rounded-md border border-slate-200">
                @else
                    <div id="placeholder-banner_image"
                         class="h-32 w-full max-w-xs bg-slate-100 border-2 border-dashed border-slate-300 rounded-md flex items-center justify-center text-slate-500 text-sm">
                        Sin imagen
                    </div>
                    <img id="preview-banner_image" class="hidden w-full max-h-64 object-cover rounded-md border border-slate-200">
                @endif
        
                <div class="flex gap-2">
                    <input id="file-banner_image" type="file" name="banner_image" accept="image/*" class="hidden">
        
                    <button type="button"
                            onclick="document.getElementById('file-banner_image').click()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer">
                        {{ $previews['banner_image'] ? 'Cambiar Imagen' : 'Subir Imagen' }}
                    </button>
        
                    @if($previews['banner_image'] && $banner)
                        <button type="button"
                                onclick="deleteImage('banner_image')"
                                class="px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition cursor-pointer">
                            Eliminar
                        </button>
                    @endif
                </div>
        
                <p class="text-xs text-slate-500 mt-2">Recomendado 1360×450px • JPG / PNG / WEBP • Máx 3MB.</p>
            </div>
        </section>

        <section class="space-y-8">

            <div>
                <label class="block text-sm font-medium text-slate-900 mb-1">Título Principal</label>
                <input type="text"
                       name="title"
                       value="{{ old('title', $nosotros->title ?? '') }}"
                       class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-900 mb-1">Descripción Principal</label>
                <textarea
                    name="description"
                    id="description"
                    class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm h-32 bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none"
                >{{ old('description', $nosotros->description ?? '') }}</textarea>
            </div>

            <div class="space-y-2 w-[500px]">
                <h4 class="text-sm font-medium text-slate-900">Imagen Principal</h4>

                @if($previews['image'])
                    <img id="preview-image"
                         src="{{ $previews['image'] }}"
                         class="w-full max-h-64 object-cover rounded-md border border-slate-200">
                @else
                    <div id="placeholder-image"
                         class="h-32 w-full max-w-xs bg-slate-100 border-2 border-dashed border-slate-300 rounded-md flex items-center justify-center text-slate-500 text-sm">
                        Sin imagen
                    </div>
                    <img id="preview-image" class="hidden w-full max-h-64 object-cover rounded-md border border-slate-200">
                @endif

                <div class="flex gap-2">
                    <input id="file-image" type="file" name="image" accept="image/*" class="hidden">

                    <button type="button"
                            onclick="document.getElementById('file-image').click()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer">
                        {{ $previews['image'] ? 'Cambiar Imagen' : 'Subir Imagen' }}
                    </button>

                    @if($previews['image'] && $nosotros)
                        <button type="button"
                                onclick="deleteImage('image')"
                                class="px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition cursor-pointer">
                            Eliminar
                        </button>
                    @endif
                </div>

                <p class="text-xs text-slate-500 mt-2">Resolución ideal 800×600px, hasta 3MB.</p>
            </div>

        </section>



        <section class="space-y-6">
            <h3 class="text-[24px] font-semibold text-slate-900">Características</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                @foreach([1,2,3] as $n)
                <div class="bg-white border border-slate-200 rounded-md p-5 shadow-sm space-y-5 flex flex-col h-full">

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-800">Título</label>
                        <input type="text"
                               name="title_{{ $n }}"
                               value="{{ old('title_'.$n, $nosotros->{'title_'.$n} ?? '') }}"
                               class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-800">Descripción</label>
                        <textarea
                            name="description_{{ $n }}"
                            id="description_{{ $n }}"
                            rows="3"
                            class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600"
                        >{{ old('description_'.$n, $nosotros->{'description_'.$n} ?? '') }}</textarea>
                    </div>

                    <div class="space-y-2">
                        <h5 class="text-sm font-medium text-slate-800">Imagen/Icono</h5>

                        @if($previews["image_$n"])
                            <img id="preview-image_{{ $n }}" src="{{ $previews["image_$n"] }}"
                                 class="w-full h-24 object-contain rounded-md border border-slate-200">
                        @else
                            <div id="placeholder-image_{{ $n }}"
                                 class="h-24 w-full bg-slate-100 border-2 border-dashed border-slate-300 rounded-md flex items-center justify-center text-slate-500 text-sm">
                                Sin imagen
                            </div>
                            <img id="preview-image_{{ $n }}" class="hidden w-full h-24 object-contain rounded-md border border-slate-200">
                        @endif

                        <div class="flex gap-2">
                            <input id="file-image_{{ $n }}" type="file" name="image_{{ $n }}" accept="image/*" class="hidden">

                            <button type="button"
                                    onclick="document.getElementById('file-image_{{ $n }}').click()"
                                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition cursor-pointer">
                                {{ $previews["image_$n"] ? 'Cambiar Imagen' : 'Subir Imagen' }}
                            </button>

                            @if($previews["image_$n"] && $nosotros)
                                <button type="button"
                                        onclick="deleteImage('image_{{ $n }}')"
                                        class="px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition cursor-pointer">
                                    Eliminar
                                </button>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            <strong>Fondo transparente*</strong><br>
                            <strong>Formatos recomendados:</strong> png,svg,webp<br>
                            <strong>Tamaño recomendado:</strong> 300x300px<br>
                            <strong>Peso máximo:</strong> 2MB
                        </p>
                    </div>

                </div>
                @endforeach

            </div>

        </section>

        <div class="pt-6 border-t border-slate-200">
            <button type="submit"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition cursor-pointer">
                Guardar Cambios
            </button>
        </div>

    </form>

    <style>
        @keyframes fadeIn { from {opacity:0; transform:translateY(8px)} to {opacity:1; transform:translateY(0)} }
        .animate-fadeIn { animation: fadeIn .35s ease; }
    </style>

</div>

<script>
function deleteImage(field) {
    if (!confirm('¿Eliminar esta imagen?')) return;
    fetch("{{ url('admin/nosotros/image') }}/" + field, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    }).then(() => location.reload());
}

function setupImagePreview(inputId, imgId, placeholderId) {
    const fileInput = document.getElementById(inputId);
    const previewImg = document.getElementById(imgId);
    const placeholder = document.getElementById(placeholderId);

    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const url = URL.createObjectURL(file);
            previewImg.src = url;
            previewImg.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    setupImagePreview('file-banner_image', 'preview-banner_image', 'placeholder-banner_image');
    setupImagePreview('file-image', 'preview-image', 'placeholder-image');
    setupImagePreview('file-image_1', 'preview-image_1', 'placeholder-image_1');
    setupImagePreview('file-image_2', 'preview-image_2', 'placeholder-image_2');
    setupImagePreview('file-image_3', 'preview-image_3', 'placeholder-image_3');


    ClassicEditor.create(document.querySelector('#description'));
    ClassicEditor.create(document.querySelector('#description_1'));
    ClassicEditor.create(document.querySelector('#description_2'));
    ClassicEditor.create(document.querySelector('#description_3'));
    ClassicEditor.create(document.querySelector('#description_4'));

});
</script>

@endsection