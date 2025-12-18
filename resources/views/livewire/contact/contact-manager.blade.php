@extends('layouts.admin')

@section('content')

<div class="space-y-6 pb-8 bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-12">

    <form id="contact-form" enctype="multipart/form-data" class="space-y-8">
        @csrf
        <section class="space-y-8">
            <h3 class="text-[24px] font-semibold text-slate-900">Banner de Sección</h3>
        
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

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-3 ">Información de Contacto</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dirección </label>
                    <input type="text" name="direction_adm"
                           value="{{ $contact->direction_adm ?? '' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Horarios</label>
                    <input type="text" name="direction_sale"
                           value="{{ $contact->direction_sale ?? '' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp 1 (WhatsApp flotante)</label>
                    <input type="text" name="wssp"
                           value="{{ $contact->wssp ?? '' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp 2</label>
                    <input type="text" name="phone_amd"
                           value="{{ $contact->phone_amd ?? '' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email </label>
                    <input type="email" name="mail_adm"
                           value="{{ $contact->mail_adm ?? '' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                </div>


            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-3 ">Redes Sociales</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                @foreach(['facebook','insta','linkedin','youtube'] as $rs)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ ucfirst($rs) }}</label>
                    <input type="url" name="{{ $rs }}"
                           value="{{ $contact->$rs ?? '' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                @endforeach

            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-3 ">Configuración del Mapa</h2>

            <textarea name="frame_adm" rows="8"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm font-mono text-sm">{{ $contact->frame_adm ?? '' }}</textarea>

            @if($contact?->frame_adm)
                <div class="border border-gray-300 rounded-lg p-4 bg-gray-50 mt-4">
                    {!! $contact->frame_adm !!}
                </div>
            @endif
        </div>

        <div class="rounded-lg mt-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                @php
                    $iconTitles = [
                        1 => 'Icono Home',
                        2 => 'Icono Sección con Banner',
                        3 => 'Icono Footer',
                    ];
                @endphp
            
                @foreach([1, 2, 3] as $i)
                    @php $icon = "icono_$i"; @endphp
            
                    <div class="text-center bg-white p-4 rounded-lg border border-gray-200 flex flex-col items-center">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">
                            {{ $iconTitles[$i] }}
                        </h3>
            
                        <img id="preview_icono_{{ $i }}"
                             src="{{ $contact?->$icon ? Storage::url($contact->$icon) : '' }}"
                             class="max-w-[200px] max-h-[150px] rounded-lg bg-gray-300 h-full object-contain border-2 border-gray-300 mb-4 {{ $contact?->$icon ? '' : 'hidden' }}">
            
                        <input type="file"
                               id="icono_{{ $i }}_temp"
                               name="icono_{{ $i }}_temp"
                               class="hidden"
                               accept="image/*">
            
                        <div class="flex mt-auto gap-2">
                            <button type="button"
                                    onclick="document.getElementById('icono_{{ $i }}_temp').click()"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-700">
                                + Subir Imagen
                            </button>
            
                            @if($contact?->$icon)
                                <button type="button"
                                        onclick="removeIcono({{ $i }})"
                                        class="px-4 py-2 bg-red-600 text-white rounded-md cursor-pointer">
                                    Eliminar
                                </button>
                            @endif
                        </div>
            
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            <strong>Fondo transparente*</strong><br>
                            <strong>Formatos recomendados:</strong> png, svg, webp<br>
                            <strong>Tamaño recomendado:</strong> 400x200px<br>
                            <strong>Peso máximo:</strong> 2MB
                        </p>
                    </div>
                @endforeach
            
            </div>
            
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 cursor-pointer">
                Guardar Cambios
            </button>
        </div>

    </form>

</div>

<script>

    function showToast(message, type = "success") {
        const toast = document.createElement("div");
        toast.className = `fixed top-6 right-6 px-4 py-3 rounded-md shadow text-white z-50
            ${type === "success" ? "bg-green-600" : "bg-red-600"}`;
        toast.innerText = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2500);
    }
    
    document.getElementById('file-banner_image').addEventListener('change', e => {
        const file = e.target.files[0];
        if (!file) return;
        const preview = document.getElementById('preview-banner_image');
        const placeholder = document.getElementById('placeholder-banner_image');
        preview.src = URL.createObjectURL(file);
        preview.classList.remove("hidden");
        placeholder?.classList.add("hidden");
    });
    
    document.querySelectorAll('input[type=file][name^="icono_"]').forEach(input => {
        input.addEventListener('change', e => {
            if (!e.target.files.length) return;
            const preview = document.getElementById('preview_' + input.name.replace('_temp',''));
            const file = e.target.files[0];
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        });
    });
    
    document.getElementById("contact-form").addEventListener("submit", function(e) {
        e.preventDefault();
        let formData = new FormData(this);
    
        fetch("{{ route('admin.contacto.save') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body: formData
        })
        .then(async res => {
            const out = await res.text();
            if (!res.ok) { console.error(out); showToast("Error al guardar", "error"); return; }
            showToast("Datos guardados correctamente");
            setTimeout(() => location.reload(), 1000);
        })
        .catch(() => showToast("Error de conexión", "error"));
    });
    
    function removeBanner() {
        if (!confirm("¿Eliminar imagen banner?")) return;
        let body = new FormData();
        body.append("remove_image_banner", "1");
    
        fetch("{{ route('admin.contacto.save') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body
        }).then(() => { showToast("Banner eliminado"); location.reload(); });
    }
    
    function deleteImage(field) {
        if (!confirm("¿Eliminar imagen?")) return;
        let body = new FormData();
        body.append("remove_" + field, "1");
    
        fetch("{{ route('admin.contacto.save') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body
        }).then(() => { showToast("Imagen eliminada"); location.reload(); });
    }
    
    function removeIcono(i) {
        if (!confirm("¿Eliminar icono " + i + "?")) return;
        let body = new FormData();
        body.append("remove_icono_" + i, "1");
    
        fetch("{{ route('admin.contacto.save') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body
        }).then(() => { showToast("Icono eliminado"); location.reload(); });
    }
    
    </script>

@endsection
