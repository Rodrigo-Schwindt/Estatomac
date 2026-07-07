@extends('layouts.admin')

@section('content')

<div class="mx-auto py-10 animate-fadeIn space-y-10 bg-white border border-slate-200 rounded-md shadow-sm p-16 px-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-md px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md px-4 py-3">
            Hubo errores al guardar. Revisá los campos marcados.
        </div>
    @endif

    <form method="POST" action="{{ route('carrito.config.save') }}" class="space-y-12">
        @csrf

        <section class="space-y-8">
            <h3 class="text-[24px] font-semibold text-slate-900">Configuración de Textos</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-1">Título 1</label>
                    <input type="text" name="title" value="{{ old('title', $config->title ?? '') }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-1">Título 2</label>
                    <input type="text" name="title2" value="{{ old('title2', $config->title2 ?? '') }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-1">Descripción 1</label>
                    <textarea name="description" id="description" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm h-32 bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">{{ old('description', $config->description ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-900 mb-1">Descripción 2</label>
                    <textarea name="description2" id="description2" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm h-32 bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">{{ old('description2', $config->description2 ?? '') }}</textarea>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-900 mb-1">Información Importante</label>
                <textarea name="informacion" id="informacion" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm h-32 bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">{{ old('informacion', $config->informacion ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-900 mb-1">Escribinos un Mensaje</label>
                <textarea name="escribenos" id="escribenos" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm h-32 bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">{{ old('escribenos', $config->escribenos ?? '') }}</textarea>
            </div>
        </section>

        <section class="space-y-6">
            <h3 class="text-[24px] font-semibold text-slate-900">Opciones de Entrega</h3>

            <div class="space-y-4">
                @php $entregas = [
                    1 => ['label' => $config->entrega_1_label ?? 'Retiro cliente',       'costo' => $config->entrega_1_costo ?? '0.00', 'activa' => $config->entrega_1_activa ?? true],
                    2 => ['label' => $config->entrega_2_label ?? 'Reparto TodoTex',      'costo' => $config->entrega_2_costo ?? '0.00', 'activa' => $config->entrega_2_activa ?? true],
                    3 => ['label' => $config->entrega_3_label ?? 'Transporte / Expreso', 'costo' => $config->entrega_3_costo ?? '0.00', 'activa' => $config->entrega_3_activa ?? true],
                ]; @endphp
                @foreach($entregas as $i => $entrega)
                <div class="bg-white border border-slate-200 rounded-md p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-semibold text-slate-700">Opción {{ $i }}</p>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <span class="text-sm text-slate-600">Activa</span>
                            <div class="relative">
                                <input type="checkbox" name="entrega_{{ $i }}_activa" value="1" class="sr-only peer"
                                       {{ old('entrega_' . $i . '_activa', $entrega['activa']) ? 'checked' : '' }}>
                                <div class="w-10 h-6 bg-slate-200 rounded-full peer peer-checked:bg-blue-600 transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                            </div>
                        </label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-900 mb-1">Nombre</label>
                            <input type="text" name="entrega_{{ $i }}_label"
                                   value="{{ old('entrega_' . $i . '_label', $entrega['label']) }}"
                                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-900 mb-1">Costo ($)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">$</span>
                                <input type="number" name="entrega_{{ $i }}_costo" step="0.01" min="0"
                                       value="{{ old('entrega_' . $i . '_costo', $entrega['costo']) }}"
                                       class="w-full border border-slate-300 rounded-md pl-7 pr-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none" required>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <section class="space-y-6">
            <h3 class="text-[24px] font-semibold text-slate-900">Descuentos por Forma de Pago (%)</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php $pagos = [
                    'contado'       => ['label' => 'Contado',          'valor' => $config->contado ?? '10.00',       'activo' => $config->contado_activo ?? true,       'nombre' => 'contado_activo'],
                    'transferencia' => ['label' => 'Transferencia',     'valor' => $config->transferencia ?? '5.00',  'activo' => $config->transferencia_activa ?? true, 'nombre' => 'transferencia_activa'],
                    'corriente'     => ['label' => 'Cuenta Corriente',  'valor' => $config->corriente ?? '0.00',      'activo' => $config->corriente_activa ?? true,     'nombre' => 'corriente_activa'],
                ]; @endphp
                @foreach($pagos as $key => $pago)
                <div class="bg-white border border-slate-200 rounded-md p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-medium text-slate-800">{{ $pago['label'] }}</label>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <span class="text-sm text-slate-600">Activa</span>
                            <div class="relative">
                                <input type="checkbox" name="{{ $pago['nombre'] }}" value="1" class="sr-only peer"
                                       {{ old($pago['nombre'], $pago['activo']) ? 'checked' : '' }}>
                                <div class="w-10 h-6 bg-slate-200 rounded-full peer peer-checked:bg-blue-600 transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                            </div>
                        </label>
                    </div>
                    <div class="relative">
                        <input type="number" name="{{ $key }}" step="0.01" min="0" max="100"
                               value="{{ old($key, $pago['valor']) }}"
                               class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none pr-8" required>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <section class="space-y-6">
            <h3 class="text-[24px] font-semibold text-slate-900">Impuestos (%)</h3>
            <div class="max-w-md">
                <div class="bg-white border border-slate-200 rounded-md p-5 shadow-sm space-y-2">
                    <label class="block text-sm font-medium text-slate-800">IVA</label>
                    <div class="relative">
                        <input type="number" name="iva" step="0.01" min="0" max="100" value="{{ old('iva', $config->iva ?? '21.00') }}" class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none pr-8" required>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">%</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="pt-6 border-t border-slate-200">
            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition cursor-pointer">
                Guardar Cambios
            </button>
        </div>

    </form>

    <style>
        @keyframes fadeIn { from {opacity:0; transform:translateY(8px)} to {opacity:1; transform:translateY(0)} }
        .animate-fadeIn { animation: fadeIn .35s ease; }
        .ck-editor__editable { min-height: 150px; }
    </style>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const fields = ['#description', '#description2', '#informacion', '#escribenos'];
    fields.forEach(selector => {
        const element = document.querySelector(selector);
        if (element) {
            ClassicEditor.create(element).catch(error => {
                console.error(error);
            });
        }
    });
});
</script>

@endsection
