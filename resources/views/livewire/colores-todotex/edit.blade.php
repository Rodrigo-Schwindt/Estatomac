@extends('layouts.admin')

@section('content')
<div class="mx-auto space-y-8 animate-fadeIn">
    <div class="flex justify-between items-center pb-2">
        <h2 class="text-xl font-semibold text-slate-900">
            Editar Color #{{ $color->id }}
        </h2>
        <a href="{{ route('colores-todotex.index') }}"
           class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 hover:bg-slate-50 transition duration-150 cursor-pointer">
            ← Volver a la lista
        </a>
    </div>

    <div id="alertContainer"></div>

    <form id="colorForm" class="bg-white rounded-md border border-slate-200 p-6 space-y-6 shadow-sm">
        @csrf

        <div>
            <label for="titulo" class="block text-sm font-medium text-slate-900 mb-2">Título <span class="text-red-500">*</span></label>
            <input type="text"
                   id="titulo"
                   name="titulo"
                   value="{{ $color->titulo }}"
                   placeholder="Ingresa el título del color"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
            <p id="titulo-error" class="mt-1 text-red-600 text-sm hidden"></p>
        </div>

        <div>
            <label for="codigo_color" class="block text-sm font-medium text-slate-900 mb-2">Código color <span class="text-red-500">*</span></label>
            <input type="text"
                   id="codigo_color"
                   name="codigo_color"
                   maxlength="4"
                   value="{{ $color->codigo_color }}"
                   placeholder="Ej: 4039"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white uppercase focus:ring-2 focus:ring-blue-600 focus:outline-none">
            <p id="codigo_color-error" class="mt-1 text-red-600 text-sm hidden"></p>
        </div>

        <div>
            <label for="orden" class="block text-sm font-medium text-slate-900 mb-2">Orden</label>
            <input type="number"
                   id="orden"
                   name="orden"
                   min="0"
                   step="1"
                   value="{{ $color->orden }}"
                   placeholder="Ej: 1"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
            <p id="orden-error" class="mt-1 text-red-600 text-sm hidden"></p>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-200">
            <a href="{{ route('colores-todotex.index') }}"
               class="px-6 py-2 border border-slate-300 rounded-md text-slate-700 hover:bg-slate-50 transition duration-150 cursor-pointer">
                Cancelar
            </a>
            <button type="submit"
                    id="submitBtn"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="submitText">Actualizar Color</span>
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
    const form = document.getElementById('colorForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoading = document.getElementById('submitLoading');
    const codigoInput = document.getElementById('codigo_color');

    codigoInput.addEventListener('input', function() {
        this.value = this.value.replace(/\s+/g, '').slice(0, 4);
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(form);

        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');
        submitLoading.classList.add('flex');

        fetch(`{{ route('colores-todotex.update', $color->id) }}`, {
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
            } else if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorEl = document.getElementById(`${field}-error`);
                    if (errorEl) {
                        errorEl.textContent = Array.isArray(data.errors[field]) ? data.errors[field][0] : data.errors[field];
                        errorEl.classList.remove('hidden');
                    }
                });
            } else {
                showAlert(data.message || 'Error al actualizar el color', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error al actualizar el color', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            submitLoading.classList.add('hidden');
            submitLoading.classList.remove('flex');
        });
    });

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
