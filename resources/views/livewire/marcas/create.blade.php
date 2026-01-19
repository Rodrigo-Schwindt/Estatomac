@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-fadeIn">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-slate-900">Crear Marca</h2>
        <a href="{{ route('marcas.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <form action="{{ route('marcas.store') }}" method="POST" class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-6">
        @csrf

        <div class="space-y-6">
            <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">Información de la Marca</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-2">
                        Título <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('title') border-red-500 @enderror"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="order" class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
                    <input type="text" 
                           name="order" 
                           id="order" 
                           value="{{ old('order') }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('order') border-red-500 @enderror">
                    @error('order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Modelos --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b pb-2">
                <h3 class="text-lg font-semibold text-slate-900">Modelos</h3>
                <button type="button" 
                        onclick="addModelo()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Agregar Modelo
                </button>
            </div>

            <div id="modelosContainer" class="space-y-4">
                {{-- Los modelos se agregarán aquí dinámicamente --}}
            </div>
        </div>

        <div class="flex gap-4 pt-6 border-t">
            <button type="submit" 
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                Crear Marca
            </button>
            <a href="{{ route('marcas.index') }}" 
               class="px-6 py-2.5 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>

<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:translateY(0) } }
.animate-fadeIn { animation: fadeIn .35s ease; }
</style>

<script>
let modeloIndex = 0;

function addModelo() {
    const container = document.getElementById('modelosContainer');
    const index = modeloIndex++;
    
    const html = `
        <div class="modelo-item p-4 border border-slate-200 rounded-md bg-slate-50 space-y-4" data-index="${index}">
            <div class="flex items-center justify-between">
                <h4 class="font-medium text-slate-700">Modelo #${index + 1}</h4>
                <button type="button" 
                        onclick="removeModelo(${index})"
                        class="text-red-600 hover:text-red-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Título <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="modelos[${index}][title]" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
                    <input type="text" 
                           name="modelos[${index}][order]" 
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
}

function removeModelo(index) {
    const item = document.querySelector(`.modelo-item[data-index="${index}"]`);
    if (item) {
        item.remove();
    }
}
</script>
@endsection