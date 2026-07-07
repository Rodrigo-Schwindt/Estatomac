@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Parámetros del Sistema</h1>
        <p class="text-gray-500 text-sm mt-1">Configuración general de la zona privada B2B.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.parametros.save') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Modo mantenimiento --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Acceso a la Zona Privada</h2>

            <div class="flex items-start justify-between pb-5 border-b border-gray-100">
                <div>
                    <p class="text-sm font-medium text-gray-800">Modo mantenimiento</p>
                    <p class="text-xs text-gray-500 mt-0.5">Bloquea el acceso a clientes y vendedores mostrando una pantalla de mantenimiento.</p>
                    @if(!$parametro->en_mantenimiento && $sesionesActivas > 0)
                        <p class="text-xs text-amber-600 mt-2">
                            <strong>{{ $sesionesActivas }}</strong> usuario(s) con sesión activa en la zona privada.
                        </p>
                    @endif
                </div>
                <label class="relative inline-flex items-center cursor-pointer ml-6 shrink-0">
                    <input type="hidden" name="en_mantenimiento" value="0">
                    <input type="checkbox" name="en_mantenimiento" value="1" id="toggle-mantenimiento"
                           {{ $parametro->en_mantenimiento ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-14 h-8 bg-gray-300 rounded-full peer peer-checked:bg-amber-500 transition-colors duration-300 relative">
                        <span class="absolute left-1 top-1 w-6 h-6 bg-white rounded-full transition-transform duration-300
                                     {{ $parametro->en_mantenimiento ? 'translate-x-6' : '' }}"></span>
                    </div>
                    <span class="ml-3 text-sm font-medium {{ $parametro->en_mantenimiento ? 'text-amber-600' : 'text-gray-500' }}" id="mantenimiento-label">
                        {{ $parametro->en_mantenimiento ? 'ACTIVO' : 'Inactivo' }}
                    </span>
                </label>
            </div>

            <div class="pt-4 pb-2" id="cerrar-sesiones-wrapper" style="display: {{ $parametro->en_mantenimiento ? 'none' : 'block' }};">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="cerrar_sesiones" value="1" class="mt-0.5 rounded border-gray-300 text-amber-500 focus:ring-amber-500" {{ $sesionesActivas > 0 ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700">
                        Cerrar sesiones activas al activar mantenimiento
                        <span class="block text-xs text-gray-500">Cualquier cliente o vendedor con sesión abierta será desconectado.</span>
                    </span>
                </label>
            </div>

            <div class="pt-5">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email de Soporte de Ventas</label>
                <input type="email" name="email_soporte" value="{{ old('email_soporte', $parametro->email_soporte) }}"
                       placeholder="soporte@ejemplo.com"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email_soporte') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1">Aparece como link de contacto en los mensajes de error.</p>
                @error('email_soporte') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Seguridad de login --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Seguridad del Login</h2>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Intentos fallidos permitidos
                    </label>
                    <input type="number" name="int_fallidos" value="{{ old('int_fallidos', $parametro->int_fallidos) }}"
                           min="1" max="20"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Después de N intentos fallidos se bloquea el acceso.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Duración del bloqueo (segundos)
                    </label>
                    <input type="number" name="delay_error" value="{{ old('delay_error', $parametro->delay_error) }}"
                           min="30" max="3600"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Ej: 300 = 5 minutos.</p>
                </div>
            </div>
        </div>

        {{-- Contraseñas --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Política de Contraseñas</h2>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Días de validez de contraseña
                    </label>
                    <input type="number" name="dias_passwd" value="{{ old('dias_passwd', $parametro->dias_passwd) }}"
                           min="0" max="3650"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">0 = sin expiración.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Longitud mínima de contraseña
                    </label>
                    <input type="number" name="largo_passwd" value="{{ old('largo_passwd', $parametro->largo_passwd) }}"
                           min="4" max="32"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Mínimo de caracteres exigido.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-8 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                Guardar cambios
            </button>
        </div>
    </form>
</div>

<style>
    #toggle-mantenimiento:checked + div span { transform: translateX(1.5rem); }
</style>
<script>
    (function () {
        const toggle  = document.getElementById('toggle-mantenimiento');
        const label   = document.getElementById('mantenimiento-label');
        const wrapper = document.getElementById('cerrar-sesiones-wrapper');
        const activas = {{ (int) ($sesionesActivas ?? 0) }};

        toggle.addEventListener('change', function () {
            label.textContent = this.checked ? 'ACTIVO' : 'Inactivo';
            label.className   = this.checked ? 'ml-3 text-sm font-medium text-amber-600' : 'ml-3 text-sm font-medium text-gray-500';
            if (wrapper) wrapper.style.display = this.checked ? 'none' : 'block';

            if (this.checked && activas > 0) {
                const ok = confirm('Hay ' + activas + ' usuario(s) con sesión activa en la zona privada. ¿Continuar y cerrar sus sesiones?');
                if (!ok) {
                    this.checked = false;
                    label.textContent = 'Inactivo';
                    label.className = 'ml-3 text-sm font-medium text-gray-500';
                    if (wrapper) wrapper.style.display = 'block';
                }
            }
        });
    })();
</script>
@endsection
