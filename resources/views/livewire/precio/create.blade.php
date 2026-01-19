@extends('layouts.admin')

@section('content')
<div class="mx-auto space-y-8 animate-fadeIn">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">Subir Nueva Lista de Precios</h2>
        <a href="{{ route('precios.index') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 hover:bg-slate-50 transition duration-150 cursor-pointer">
            ← Volver a la lista
        </a>
    </div>

    <form id="precioForm" enctype="multipart/form-data" class="bg-white rounded-md border border-slate-200 p-6 space-y-6 shadow-sm">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Título de la Lista *</label>
            <input type="text" 
                   id="title" 
                   name="title" 
                   placeholder="Ej: Lista de Precios Distribución Enero" 
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
            <p id="title-error" class="mt-1 text-red-600 text-sm hidden"></p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Archivo (PDF o Excel) *</label>
            <input type="file" 
                   id="archivo" 
                   name="archivo" 
                   accept=".pdf,.xls,.xlsx"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none file:bg-blue-600 file:text-white file:px-4 file:py-2 file:rounded-md file:border-0 file:cursor-pointer cursor-pointer">
            
            <p id="archivo-error" class="mt-2 text-red-600 text-sm hidden"></p>

            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                <strong>Formatos permitidos:</strong> PDF, XLS, XLSX (Excel)<br>
                <strong>Peso máximo:</strong> 10MB
            </p>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
            <a href="{{ route('precios.index') }}" class="px-5 py-2 border border-slate-300 rounded-md text-sm text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                Cancelar
            </a>

            <button type="submit" 
                    id="submitBtn" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition cursor-pointer disabled:opacity-50">
                <span id="submitText">Subir Lista</span>
                <span id="submitLoading" class="hidden flex items-center gap-2">
                    <div class="h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    Subiendo...
                </span>
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('precioForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const text = document.getElementById('submitText');
    const loading = document.getElementById('submitLoading');
    
    // Limpiar errores
    document.querySelectorAll('[id$="-error"]').forEach(el => el.classList.add('hidden'));

    btn.disabled = true;
    text.classList.add('hidden');
    loading.classList.remove('hidden');

    fetch('{{ route("precios.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: new FormData(this)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else if (data.errors) {
            Object.keys(data.errors).forEach(field => {
                const errorEl = document.getElementById(`${field}-error`);
                if (errorEl) {
                    errorEl.textContent = data.errors[field][0];
                    errorEl.classList.remove('hidden');
                }
            });
        }
    })
    .catch(error => console.error('Error:', error))
    .finally(() => {
        btn.disabled = false;
        text.classList.remove('hidden');
        loading.classList.add('hidden');
    });
});
</script>
@endsection