@extends('layouts.admin')

@section('content')
<div class="mx-auto space-y-8 animate-fadeIn">
    <div class="flex justify-between items-center pb-2">
        <h2 class="text-xl font-semibold text-slate-900">
            Editar Slider #{{ $slider->id }}
        </h2>
        <a href="{{ route('sliders.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 hover:bg-slate-50 transition duration-150 cursor-pointer">
            ← Volver a la lista
        </a>
    </div>

    <form id="sliderForm" enctype="multipart/form-data" class="bg-white rounded-md border border-slate-200 p-6 space-y-6 shadow-sm">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-900 mb-2">
                Archivo Actual
            </label>
            <div class="flex items-center space-x-4 p-4 border border-slate-200 rounded-md bg-slate-50">
                @php
                    $ext = strtolower(pathinfo($slider->image, PATHINFO_EXTENSION));
                    $isVideo = in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'avi']);
                @endphp

                @if ($isVideo)
                    <video class="h-32 w-auto object-cover rounded-md" controls>
                        <source src="{{ Storage::url($slider->image) }}" type="video/{{ $ext }}">
                        Tu navegador no soporta el elemento video.
                    </video>
                @else
                    <img src="{{ Storage::url($slider->image) }}" 
                         alt="{{ $slider->title }}" 
                         class="h-32 w-auto object-cover rounded-md border">
                @endif

                <div>
                    <p class="text-sm text-slate-700">
                        <strong>Archivo actual:</strong> {{ basename($slider->image) }}
                    </p>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ $isVideo ? 'Video' : 'Imagen' }} actual del slider
                    </p>
                    <p class="text-xs text-slate-500">
                        Para cambiar, selecciona un nuevo archivo abajo
                    </p>
                </div>
            </div>
        </div>

        <div>
            <label for="image" class="block text-sm font-medium text-slate-900 mb-2">
                Nueva Imagen/Video (Opcional)
            </label>
            
            <div id="filePreview" class="mb-4 p-4 border border-slate-200 rounded-md bg-slate-50 hidden">
                <img id="imagePreview" src="" class="h-48 w-full object-cover rounded-md hidden">
                <video id="videoPreview" class="h-48 w-full object-cover rounded-md hidden" controls></video>

                <div class="mt-2 text-sm text-slate-700">
                    <p><strong>Nuevo archivo:</strong> <span id="newFileName"></span></p>
                    <p><strong>Tamaño:</strong> <span id="newFileSize"></span> MB</p>
                    <p><strong>Tipo:</strong> <span id="newFileType"></span></p>
                </div>
            </div>

            <input type="file" 
                   id="image"
                   name="image"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none file:bg-blue-600 file:text-white file:px-4 file:py-2 file:rounded-md file:border-0 file:cursor-pointer cursor-pointer"
                   accept=".jpeg,.jpg,.png,.gif,.svg,.mp4,.webm,.ogg,.mov,.avi">
            
            <p id="image-error" class="mt-2 text-red-600 text-sm hidden"></p>
            
            <div id="fileLoading" class="mt-2 flex items-center space-x-2 text-sm text-slate-600 hidden">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                <span>Procesando archivo...</span>
            </div>
            
            <p class="text-xs text-slate-500 mt-2">
                <strong>Formatos permitidos:</strong> JPEG, PNG, JPG, GIF, SVG, MP4, WebM, OGG, MOV, AVI<br>
                <strong>Tamaño recomendado:</strong> 1360x670px<br>  
                <strong>Peso máximo:</strong> 100MB
            </p>
        </div>

        <div>
            <label for="title" class="block text-sm font-medium text-slate-900 mb-2">
                Título 
            </label>
            <input type="text" 
                   id="title"
                   name="title"
                   value="{{ $slider->title }}"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none"
                   placeholder="Ingresa el título del slider">
            <p id="title-error" class="mt-1 text-red-600 text-sm hidden"></p>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-slate-900 mb-2">
                Descripción
            </label>
            <textarea id="description"
                      name="description"
                      rows="3"
                      class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none"
                      placeholder="Descripción opcional del slider">{{ $slider->description }}</textarea>
            <p id="description-error" class="mt-1 text-red-600 text-sm hidden"></p>
        </div>


        <div>
            <label for="orden" class="block text-sm font-medium text-slate-900 mb-2">
                Orden *
            </label>
            <input type="text" 
                   id="orden"
                   name="orden"
                   value="{{ $slider->orden }}"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none font-mono uppercase"
                   placeholder="Ej: AA, AB, AC, etc.">
            <p id="orden-error" class="mt-1 text-red-600 text-sm hidden"></p>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-200">
            <a href="{{ route('sliders.index') }}" 
               class="px-6 py-2 border border-slate-300 rounded-md text-slate-700 hover:bg-slate-50 transition duration-150 cursor-pointer">
                Cancelar
            </a>
            <button type="submit" 
                    id="submitBtn"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="submitText">Actualizar Slider</span>
                <span id="submitLoading" class="hidden flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Actualizando...
                </span>
            </button>
        </div>
    </form>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>

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
    const newFileName = document.getElementById('newFileName');
    const newFileSize = document.getElementById('newFileSize');
    const newFileType = document.getElementById('newFileType');
    const fileLoading = document.getElementById('fileLoading');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoading = document.getElementById('submitLoading');
    const sliderId = {{ $slider->id }};

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

            newFileName.textContent = file.name;
            newFileSize.textContent = (file.size / 1024 / 1024).toFixed(2);
            newFileType.textContent = file.type;
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

        fetch(`{{ route('sliders.update', $slider->id) }}`, {
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
                    showAlert(data.message || 'Error al actualizar el slider', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al actualizar el slider', 'error');
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