@extends('layouts.admin')

@section('content')
<div class="mx-auto space-y-8 animate-fadeIn">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">Crear Nuevo Slider</h2>
        <a href="{{ route('sliders.index') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 hover:bg-slate-50 transition duration-150 cursor-pointer">
            ← Volver a la lista
        </a>
    </div>

    <form id="sliderForm" enctype="multipart/form-data" class="bg-white rounded-md border border-slate-200 p-6 space-y-6 shadow-sm">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Imagen o Video *</label>

            <div id="filePreview" class="mb-4 p-4 border border-slate-200 rounded-md bg-slate-50 hidden">
                <img id="imagePreview" src="" class="w-full h-48 object-cover rounded-md hidden">
                <video id="videoPreview" class="w-full h-48 object-cover rounded-md hidden" controls></video>

                <div class="mt-3 text-sm text-slate-700 space-y-1">
                    <p><strong>Archivo:</strong> <span id="fileName"></span></p>
                    <p><strong>Tamaño:</strong> <span id="fileSize"></span> MB</p>
                    <p><strong>Tipo:</strong> <span id="fileType"></span></p>
                </div>
            </div>

            <input type="file"
                   id="image"
                   name="image"
                   accept=".jpg,.jpeg,.png,.gif,.svg,.mp4,.webm,.ogg,.mov,.avi"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none file:bg-blue-600 file:text-white file:px-4 file:py-2 file:rounded-md file:border-0 file:cursor-pointer cursor-pointer">

            <p id="image-error" class="mt-2 text-red-600 text-sm hidden"></p>

            <div id="fileLoading" class="mt-2 flex items-center gap-2 text-sm text-slate-600 hidden">
                <div class="h-4 w-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                Procesando archivo...
            </div>

            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                <strong>Formatos permitidos:</strong> JPG, PNG, GIF, SVG, MP4, WebM, OGG, MOV, AVI<br>
                <strong>Tamaño recomendado:</strong> 1360x670px<br>
                <strong>Peso máximo:</strong> 100MB
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Título </label>
            <input type="text"
                   id="title"
                   name="title"
                   placeholder="Ingresa el título"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
            <p id="title-error" class="mt-1 text-red-600 text-sm hidden"></p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Descripción</label>
            <textarea id="description"
                      name="description"
                      rows="3"
                      class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none"
                      placeholder="Descripción opcional"></textarea>
            <p id="description-error" class="mt-1 text-red-600 text-sm hidden"></p>
        </div>
                <div>
    <label class="block text-sm font-medium text-slate-900 mb-2">URL (opcional)</label>

    <input type="url"
           id="url"
           name="url"
           placeholder="https://tudominio.com/..."
           class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">

    <p id="url-error" class="mt-1 text-red-600 text-sm hidden"></p>

    <p class="text-xs text-slate-500 mt-1">
        URL de destino del boton.
    </p>
</div>

        <div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Orden *</label>
            <input type="text"
                   id="orden"
                   name="orden"
                   placeholder="Ej: AA, AB, AC..."
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white font-mono tracking-wider uppercase focus:ring-2 focus:ring-blue-600 focus:outline-none">
            <p id="orden-error" class="mt-1 text-red-600 text-sm hidden"></p>
            <p class="text-xs text-slate-500 mt-1">Usa letras: AA, AB, AC... (orden alfabético)</p>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
            <a href="{{ route('sliders.index') }}"
               class="px-5 py-2 border border-slate-300 rounded-md text-sm text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                Cancelar
            </a>

            <button type="submit"
                    id="submitBtn"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="submitText">Crear Slider</span>
                <span id="submitLoading" class="hidden flex items-center gap-2">
                    <div class="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    Creando...
                </span>
            </button>
        </div>
    </form>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let editorDescription;

    ClassicEditor
        .create(document.querySelector('#description'))
        .then(e => editorDescription = e)
        .catch(error => console.error(error));

    const form = document.getElementById('sliderForm');
    const imageInput = document.getElementById('image');
    const filePreview = document.getElementById('filePreview');
    const imagePreview = document.getElementById('imagePreview');
    const videoPreview = document.getElementById('videoPreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileType = document.getElementById('fileType');
    const fileLoading = document.getElementById('fileLoading');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoading = document.getElementById('submitLoading');

    // File preview
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        
        if (!file) {
            filePreview.classList.add('hidden');
            return;
        }

        fileLoading.classList.remove('hidden');

        const isVideo = file.type.startsWith('video/');
        const isImage = file.type.startsWith('image/');

        const reader = new FileReader();
        reader.onload = function(e) {
            fileLoading.classList.add('hidden');

            if (isVideo) {
                videoPreview.src = e.target.result;
                videoPreview.classList.remove('hidden');
                imagePreview.classList.add('hidden');
            } else if (isImage) {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('hidden');
                videoPreview.classList.add('hidden');
            }

            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024 / 1024).toFixed(2);
            fileType.textContent = file.type;
            filePreview.classList.remove('hidden');
        };

        reader.readAsDataURL(file);
    });

    // Submit form
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        clearErrors();

        const formData = new FormData(form);
               if (editorDescription) {
            formData.set('description', editorDescription.getData());
        }

        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');

        fetch('{{ route("sliders.store") }}', {
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
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorEl = document.getElementById(`${field}-error`);
                        if (errorEl) {
                            errorEl.textContent = Array.isArray(data.errors[field]) 
                                ? data.errors[field][0] 
                                : data.errors[field];
                            errorEl.classList.remove('hidden');
                        }
                    });
                } else {
                    showAlert(data.message || 'Error al crear el slider', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al crear el slider', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            submitLoading.classList.add('hidden');
        });
    });

    function clearErrors() {
        document.querySelectorAll('[id$="-error"]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
    }

    function showAlert(message, type = 'info') {
        const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
        const alertHtml = `
            <div class="px-4 py-3 rounded-md ${bgColor} border text-sm">
                ${message}
            </div>
        `;
        
        const alertContainer = document.createElement('div');
        alertContainer.innerHTML = alertHtml;
        form.parentElement.insertBefore(alertContainer.firstElementChild, form);

        setTimeout(() => {
            alertContainer.firstElementChild?.remove();
        }, 4000);
    }
});
</script>
@endsection