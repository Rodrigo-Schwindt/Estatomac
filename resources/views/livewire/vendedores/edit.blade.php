@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Editar Vendedor</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('vendedores.update', $vendedor) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="nombre"
                        value="{{ old('nombre', $vendedor->nombre) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nombre') border-red-500 @enderror"
                        required
                    >
                    @error('nombre')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $vendedor->email) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                    >
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                    <input
                        type="text"
                        name="telefono"
                        value="{{ old('telefono', $vendedor->telefono) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Celular</label>
                    <input
                        type="text"
                        name="celular"
                        value="{{ old('celular', $vendedor->celular) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp</label>
                    <input
                        type="text"
                        name="whatsapp"
                        value="{{ old('whatsapp', $vendedor->whatsapp) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Comisión (%)</label>
                    <input
                        type="number"
                        name="comision"
                        value="{{ old('comision', $vendedor->comision) }}"
                        step="0.01"
                        min="0"
                        max="100"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('comision') border-red-500 @enderror"
                    >
                    @error('comision')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Opera en nombre de
                        <span class="text-gray-400 font-normal text-xs">(dejar vacío si opera con sus propios clientes)</span>
                    </label>
                    <select
                        name="opera_como"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('opera_como') border-red-500 @enderror"
                    >
                        <option value="">— Sin sustitución —</option>
                        @foreach($otrosVendedores as $v)
                            <option value="{{ $v->id }}" {{ old('opera_como', $vendedor->opera_como) == $v->id ? 'selected' : '' }}>
                                {{ $v->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('opera_como')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @if($vendedor->opera_como)
                        <p class="text-amber-600 text-xs mt-1">
                            Este vendedor opera actualmente en nombre de <strong>{{ $otrosVendedores->find($vendedor->opera_como)?->nombre ?? 'vendedor desconocido' }}</strong>.
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nueva contraseña
                        <span class="text-gray-400 font-normal text-xs">(dejar en blanco para no cambiar)</span>
                    </label>
                    <input
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        placeholder="••••••••"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                    >
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700">Vendedor Activo</span>
                        <div class="relative inline-block">
                            <input type="hidden" name="activo" value="0">
                            <input
                                type="checkbox"
                                name="activo"
                                value="1"
                                id="toggle-activo"
                                {{ old('activo', $vendedor->activo) ? 'checked' : '' }}
                                class="sr-only peer"
                            >
                            <label
                                for="toggle-activo"
                                class="block w-14 h-8 bg-gray-300 rounded-full cursor-pointer peer-checked:bg-green-500 transition-colors duration-300 relative"
                            >
                                <span class="absolute left-1 top-1 w-6 h-6 bg-white rounded-full transition-transform duration-300 peer-checked:translate-x-6"></span>
                            </label>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('vendedores.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Actualizar Vendedor
                </button>
            </div>
        </form>
    </div>
</div>
<style>
    #toggle-activo:checked + label span { transform: translateX(1.5rem); }
</style>
@endsection
