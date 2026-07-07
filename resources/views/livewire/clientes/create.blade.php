@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Crear Nuevo Cliente</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('clientes.store') }}" method="POST">
            @csrf

            {{-- ACCESO Y DATOS BÁSICOS --}}
            <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b">Acceso y datos básicos</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Usuario <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="usuario" value="{{ old('usuario') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('usuario') border-red-500 @enderror" required>
                    @error('usuario') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Código</label>
                    <input type="text" name="codigo" value="{{ old('codigo') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Contraseña <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" autocomplete="new-password"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror" required>
                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmar Contraseña <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" autocomplete="new-password"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>

            {{-- DATOS DE LA EMPRESA --}}
            <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b">Datos de la empresa</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Razón Social <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nombre') border-red-500 @enderror" required>
                    @error('nombre') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Fantasía</label>
                    <input type="text" name="nombre_fantasia" value="{{ old('nombre_fantasia') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CUIT</label>
                    <input type="text" name="cuit" value="{{ old('cuit') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CUIL</label>
                    <input type="text" name="cuil" value="{{ old('cuil') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Condición IVA</label>
                    <input type="text" name="condicion_iva" value="{{ old('condicion_iva') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- CONTACTO --}}
            <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b">Contacto</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Celular</label>
                    <input type="text" name="celular" value="{{ old('celular') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Domicilio</label>
                    <input type="text" name="domicilio" value="{{ old('domicilio') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Localidad / Ciudad</label>
                    <input type="text" name="localidad" value="{{ old('localidad') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Código Postal</label>
                    <input type="text" name="codigo_postal" value="{{ old('codigo_postal') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Provincia</label>
                    <input type="text" name="provincia" value="{{ old('provincia') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- COMERCIAL --}}
            <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b">Datos comerciales</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vendedor</label>
                    <select name="vendedor_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sin vendedor</option>
                        @foreach($vendedores as $v)
                            <option value="{{ $v->id }}" {{ old('vendedor_id') == $v->id ? 'selected' : '' }}>{{ $v->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Condición de Venta</label>
                    <input type="text" name="condicion_venta" value="{{ old('condicion_venta') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Operación</label>
                    <input type="text" name="tipo_operacion" value="{{ old('tipo_operacion') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descuento (%)</label>
                    <input type="number" name="descuento" value="{{ old('descuento', 0) }}" step="0.01" min="0"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Transporte</label>
                    <input type="text" name="transporte" value="{{ old('transporte') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rubro Cliente</label>
                    <input type="text" name="rubro_cliente" value="{{ old('rubro_cliente') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Lista</label>
                    <input type="text" name="tipo_lista" value="{{ old('tipo_lista') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Canal</label>
                    <input type="text" name="canal" value="{{ old('canal') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Descuento Canal (%)</label>
                    <input type="number" name="descuento_canal" value="{{ old('descuento_canal', 0) }}" step="0.01" min="0"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700">Cliente Activo</span>
                        <div class="relative inline-block">
                            <input type="hidden" name="activo" value="0">
                            <input type="checkbox" name="activo" value="1" id="toggle-activo"
                                {{ old('activo', true) ? 'checked' : '' }} class="sr-only peer">
                            <label for="toggle-activo" class="block w-14 h-8 bg-gray-300 rounded-full cursor-pointer peer-checked:bg-green-500 transition-colors duration-300 relative">
                                <span class="absolute left-1 top-1 w-6 h-6 bg-white rounded-full transition-transform duration-300 peer-checked:translate-x-6"></span>
                            </label>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('clientes.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Guardar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
<style>
    #toggle-activo:checked + label span { transform: translateX(1.5rem); }
</style>
@endsection
