@extends('layouts.admin')

@section('content')
<div class="mx-auto space-y-8 animate-fadeIn">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-slate-900">Editar Lista: {{ $precio->title }}</h2>
        <a href="{{ route('precios.index') }}" class="text-sm text-slate-600 hover:underline">← Volver</a>
    </div>

    <form id="editPrecioForm" class="bg-white rounded-md border border-slate-200 p-6 space-y-6 shadow-sm">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Título *</label>
            <input type="text" name="title" value="{{ $precio->title }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
            <p id="title-error" class="text-red-600 text-xs mt-1 hidden"></p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Archivo Actual</label>
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded border border-slate-200 mb-2 text-sm">
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2"/></svg>
                <span class="truncate">{{ basename($precio->archivo) }}</span>
            </div>
            <label class="block text-sm font-medium text-slate-900 mb-2">Reemplazar archivo (opcional)</label>
            <input type="file" name="archivo" accept=".pdf,.xls,.xlsx" class="w-full text-sm border border-slate-300 rounded-md p-2">
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
    btn.disabled = true;
    
    fetch('{{ route("precios.update", $precio->id) }}', {
        method: 'POST', // Usamos POST con spoofing o manejamos el update en el controller como POST si prefieres
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: new FormData(this)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) window.location.href = data.redirect;
        else { /* Manejar errores de validación aquí */ }
    })
    .finally(() => btn.disabled = false);
});
</script>
@endsection