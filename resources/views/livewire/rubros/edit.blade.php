@extends('layouts.admin')

@section('content')
<div class="mx-auto space-y-8 animate-fadeIn">
    <div class="flex justify-between items-center pb-2">
        <h2 class="text-xl font-semibold text-slate-900">
            Editar Rubro #{{ $rubro->id }}
        </h2>
        <a href="{{ route('rubros.index') }}"
           class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 hover:bg-slate-50 transition duration-150 cursor-pointer">
            ← Volver a la lista
        </a>
    </div>

    <div id="alertContainer"></div>

    <form id="rubroForm" class="bg-white rounded-md border border-slate-200 p-6 space-y-6 shadow-sm">
        @csrf

        <div>
            <label for="titulo" class="block text-sm font-medium text-slate-900 mb-2">Título <span class="text-red-500">*</span></label>
            <input type="text"
                   id="titulo"
                   name="titulo"
                   value="{{ $rubro->titulo }}"
                   placeholder="Ingresa el título"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
            <p id="titulo-error" class="mt-1 text-red-600 text-sm hidden"></p>
        </div>

        <div>
            <label for="orden" class="block text-sm font-medium text-slate-900 mb-2">Orden</label>
            <input type="number"
                   id="orden"
                   name="orden"
                   min="0"
                   step="1"
                   value="{{ $rubro->orden }}"
                   placeholder="Ej: 1"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
            <p id="orden-error" class="mt-1 text-red-600 text-sm hidden"></p>
            <p class="text-xs text-slate-500 mt-1">Usa un número entero para ordenar los rubros.</p>
        </div>

        <div x-data="{ preview: null, existing: '{{ $rubro->imagen ? Storage::url($rubro->imagen) : '' }}' }">
            <label class="block text-sm font-medium text-slate-900 mb-2">Imagen banner</label>
            <div class="flex items-start gap-4">
                <label class="cursor-pointer flex flex-col items-center justify-center w-40 h-28 border-2 border-dashed border-slate-300 rounded-lg hover:border-blue-500 transition-colors bg-slate-50">
                    <svg class="w-8 h-8 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-xs text-slate-500">{{ $rubro->imagen ? 'Cambiar imagen' : 'Subir imagen' }}</span>
                    <input type="file" name="imagen" accept="image/*" class="hidden"
                           @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                </label>
                <div x-show="preview || existing" class="relative">
                    <img :src="preview || existing" alt="Banner de rubro" class="w-40 h-28 object-cover rounded-lg border border-slate-200">
                    @if($rubro->imagen)
                        <button type="button" id="btnDeleteImagen"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600"
                                title="Eliminar imagen">✕</button>
                    @else
                        <button type="button" @click="preview = null; existing = null; $el.closest('[x-data]').querySelector('input[type=file]').value = ''"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">✕</button>
                    @endif
                </div>
            </div>
            <p id="imagen-error" class="mt-1 text-red-600 text-sm hidden"></p>
            <p class="text-xs text-slate-500 mt-1">JPG, PNG, WEBP · Máx. 3 MB. Se muestra como banner al filtrar por este rubro.</p>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-200">
            <a href="{{ route('rubros.index') }}"
               class="px-6 py-2 border border-slate-300 rounded-md text-slate-700 hover:bg-slate-50 transition duration-150 cursor-pointer">
                Cancelar
            </a>
            <button type="submit"
                    id="submitBtn"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="submitText">Actualizar Rubro</span>
                <span id="submitLoading" class="hidden items-center gap-2">
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
    const form = document.getElementById('rubroForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoading = document.getElementById('submitLoading');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(form);

        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');
        submitLoading.classList.add('flex');

        fetch(`{{ route('rubros.update', $rubro->id) }}`, {
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
                        const errorEl = document.getElementById(`${field}-error`);
                        if (errorEl) {
                            errorEl.textContent = Array.isArray(data.errors[field]) ? data.errors[field][0] : data.errors[field];
                            errorEl.classList.remove('hidden');
                        }
                    });
                } else {
                    showAlert(data.message || 'Error al actualizar el rubro', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al actualizar el rubro', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            submitLoading.classList.add('hidden');
            submitLoading.classList.remove('flex');
        });
    });

    const btnDeleteImagen = document.getElementById('btnDeleteImagen');
    if (btnDeleteImagen) {
        btnDeleteImagen.addEventListener('click', function() {
            if (!confirm('¿Eliminar la imagen de este rubro?')) return;
            fetch(`{{ route('rubros.deleteImagen', $rubro->id) }}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btnDeleteImagen.closest('[x-data]').__x.$data.existing = null;
                    btnDeleteImagen.remove();
                    showAlert(data.message, 'success');
                }
            });
        });
    }

    function clearErrors() {
        document.querySelectorAll('[id$="-error"]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
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
