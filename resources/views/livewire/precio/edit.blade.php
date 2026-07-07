@extends('layouts.admin')

@section('content')
<div class="mx-auto space-y-8 animate-fadeIn">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-slate-900">Editar Lista: {{ $precio->title }}</h2>
        <a href="{{ route('precios.index') }}" class="text-sm text-slate-600 hover:underline">← Volver</a>
    </div>

    <form id="editPrecioForm" class="bg-white rounded-md border border-slate-200 p-6 space-y-6 shadow-sm">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-900 mb-2">Título *</label>
                <input type="text" name="title" id="title" value="{{ $precio->title }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                <p id="title-error" class="text-red-600 text-xs mt-1 hidden"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-900 mb-2">Número</label>
                <input type="number" name="numero" id="numero" value="{{ $precio->numero }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-900 mb-2">Vigente desde</label>
                <input type="date" name="fecha_desde" value="{{ optional($precio->fecha_desde)->format('Y-m-d') }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
            </div>

            <div class="flex items-end">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="vigente_sn" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600" {{ $precio->vigente_sn ? 'checked' : '' }}>
                    <span class="text-sm text-slate-700">Activar como lista vigente</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Observaciones</label>
            <textarea name="observaciones" rows="2" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600 outline-none">{{ $precio->observaciones }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Archivo Actual</label>
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded border border-slate-200 mb-2 text-sm">
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2"/>
                </svg>
                <span class="truncate">{{ basename($precio->archivo) }}</span>
            </div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Reemplazar archivo (opcional)</label>
            <input type="file" name="archivo" id="archivo" accept=".pdf,.xls,.xlsx" class="w-full text-sm border border-slate-300 rounded-md p-2">
            <p id="archivo-error" class="text-red-600 text-xs mt-1 hidden"></p>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="submit" id="submitBtn" class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('editPrecioForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('submitBtn');
    const formData = new FormData(this);
    
    // Limpiar errores
    document.querySelectorAll('[id$="-error"]').forEach(el => el.classList.add('hidden'));
    
    btn.disabled = true;
    btn.textContent = 'Guardando...';
    
    fetch('{{ route("precios.update", $precio->id) }}', {
        method: 'POST',
        headers: { 
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            window.location.href = data.redirect;
        } else if(data.errors) {
            Object.keys(data.errors).forEach(field => {
                const errorEl = document.getElementById(`${field}-error`);
                if (errorEl) {
                    errorEl.textContent = data.errors[field][0];
                    errorEl.classList.remove('hidden');
                }
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al actualizar');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Guardar Cambios';
    });
});
</script>
@endsection