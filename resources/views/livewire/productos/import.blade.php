@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fadeIn">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-slate-900">Actualizar Precios Masivamente</h2>
        <a href="{{ route('productos.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-6">
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">Instrucciones</h3>
            
            <div class="space-y-3 text-sm text-slate-700">
                <p><strong>1. Descarga la plantilla:</strong></p>
                <a href="{{ route('productos.template') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Descargar Plantilla CSV
                </a>

                <p class="mt-4"><strong>2. Completa la información:</strong></p>
                <ul class="list-disc list-inside space-y-1 ml-4">
                    <li><strong>code:</strong> Código del producto (obligatorio, debe existir)</li>
                    <li><strong>title:</strong> Nuevo título del producto (opcional)</li>
                    <li><strong>precio:</strong> Nuevo precio (opcional, formato: 100.50)</li>
                    <li><strong>descuento:</strong> Descuento (opcional, formato: 10.00)</li>
                    <li><strong>visible:</strong> Visibilidad (1 = visible, 0 = oculto)</li>
                    <li><strong>nuevo:</strong> Tipo (nuevo o recambio)</li>
                    <li><strong>oferta:</strong> En oferta (1 = sí, 0 = no)</li>
                    <li><strong>importados:</strong> Producto importado (1 = sí, 0 = no)</li>
                </ul>

                <p class="mt-4"><strong>3. Sube el archivo:</strong></p>
                <p class="text-slate-600">Solo se actualizarán los productos que tengan un código coincidente en la base de datos.</p>
            </div>
        </div>

        <form action="{{ route('productos.import') }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-6 border-t pt-6">
            @csrf

            <div>
                <label for="file" class="block text-sm font-medium text-slate-700 mb-2">
                    Archivo Excel/CSV <span class="text-red-500">*</span>
                </label>
                <input type="file" 
                       name="file" 
                       id="file" 
                       accept=".xlsx,.xls,.csv"
                       class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('file') border-red-500 @enderror"
                       required>
                <p class="mt-1 text-xs text-slate-500">Formatos permitidos: XLSX, XLS, CSV (máximo 5MB)</p>
                @error('file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4 pt-6 border-t">
                <button type="submit" 
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Importar y Actualizar
                </button>
                <a href="{{ route('productos.index') }}" 
                   class="px-6 py-2.5 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Notas importantes:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Solo se actualizarán los campos que contengan información</li>
                    <li>Los productos se identifican por su código único</li>
                    <li>Si un código no existe, se omitirá esa fila</li>
                    <li>Verifica el formato de los datos antes de importar</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>
@endsection