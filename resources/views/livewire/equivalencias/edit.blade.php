{{-- resources/views/admin/equivalencias/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-8 animate-fadeIn">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-slate-900">Editar Equivalencia</h2>
        <a href="{{ route('equivalencias.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <form action="{{ route('equivalencias.update', $equivalencia) }}" method="POST" class="bg-white border border-slate-200 rounded-md shadow-sm p-8 space-y-6">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <h3 class="text-lg font-semibold text-slate-900 border-b pb-2">Información de la Equivalencia</h3>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="producto_id" class="block text-sm font-medium text-slate-700 mb-2">
                        Producto <span class="text-red-500">*</span>
                    </label>
                    <select name="producto_id" 
                            id="producto_id" 
                            class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('producto_id') border-red-500 @enderror"
                            required>
                        <option value="">Seleccionar producto</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" 
                                    data-code="{{ $producto->code }}"
                                    {{ old('producto_id', $equivalencia->producto_id) == $producto->id ? 'selected' : '' }}>
                                {{ $producto->title }} {{ $producto->code ? '(' . $producto->code . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('producto_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-2">
                        Título <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $equivalencia->title) }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('title') border-red-500 @enderror"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-slate-700 mb-2">Código</label>
                    <input type="text" 
                           name="code" 
                           id="code" 
                           value="{{ old('code', $equivalencia->code) }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="order" class="block text-sm font-medium text-slate-700 mb-2">Orden</label>
                    <input type="text" 
                           name="order" 
                           id="order" 
                           value="{{ old('order', $equivalencia->order) }}"
                           class="w-full px-4 py-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 @error('order') border-red-500 @enderror">
                    @error('order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex gap-4 pt-6 border-t">
            <button type="submit" 
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                Actualizar Equivalencia
            </button>
            <a href="{{ route('equivalencias.index') }}" 
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

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single {
    border: 1px solid rgb(203 213 225);
    border-radius: 0.375rem;
    height: 42px;
    padding: 0.5rem 1rem;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 26px;
    padding-left: 0;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
}

.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: rgb(37 99 235);
    outline: 2px solid rgb(37 99 235);
    outline-offset: 2px;
}

.select2-dropdown {
    border: 1px solid rgb(203 213 225);
    border-radius: 0.375rem;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: rgb(37 99 235);
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#producto_id').select2({
        placeholder: 'Buscar producto por nombre o código...',
        allowClear: true,
        language: {
            noResults: function() {
                return "No se encontraron productos";
            },
            searching: function() {
                return "Buscando...";
            }
        }
    });
});
</script>
@endsection