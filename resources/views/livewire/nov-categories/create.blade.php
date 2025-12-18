@extends('layouts.admin')

@section('content')

<div class="mx-auto space-y-6 animate-fadeIn">

    {{-- FORMULARIO --}}
    <form action="{{ route('novcategories.store') }}"
          method="POST"
          class="border border-slate-200 rounded-md p-6 space-y-6 bg-white shadow-sm">
        @csrf

        <div class="flex justify-between items-center pb-4">
            <h2 class="text-2xl font-bold text-slate-900">Nueva Categoría de Novedades</h2>

            <a href="{{ route('novcategories.index') }}"
               class="px-4 py-2 border border-slate-300 rounded-md hover:bg-slate-100 transition cursor-pointer">
                ← Volver a la lista
            </a>
        </div>

        {{-- TITULO --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-slate-700">Título *</label>
            <input type="text"
                   name="title"
                   value="{{ old('title') }}"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600 focus:outline-none">
            @error('title')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- ORDEN --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-slate-700">Orden (alfabético)</label>
            <input type="text"
                   name="orden"
                   value="{{ old('orden') }}"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm uppercase font-mono focus:ring-2 focus:ring-blue-600 focus:outline-none">
            @error('orden')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- BOTONES --}}
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">

            <a href="{{ route('novcategories.index') }}"
               class="px-6 py-2 border border-slate-300 rounded-md text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                Cancelar
            </a>

            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer">
                Crear Categoría
            </button>
        </div>

    </form>

<style>
@keyframes fadeIn { from {opacity: 0; transform: translateY(6px)} to {opacity: 1; transform: translateY(0)} }
.animate-fadeIn { animation: fadeIn .28s ease; }
</style>

</div>

@endsection
