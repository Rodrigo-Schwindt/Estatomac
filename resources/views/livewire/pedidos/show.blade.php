@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detalle del Pedido #{{ $pedido->numero_pedido }}</h1>
            <p class="text-sm text-gray-500 mt-1">Realizado el {{ $pedido->fecha_compra->format('d/m/Y') }}</p>
        </div>
        
        <div class="flex gap-3">
            <a 
                href="{{ route('admin.pedidos.factura', $pedido->id) }}"
                target="_blank"
                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium inline-flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Descargar Factura PDF
            </a>
            
            <a 
                href="{{ route('admin.pedidos.index') }}"
                class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-medium"
            >
                Volver al listado
            </a>
        </div>
    </div>

    <!-- Estado del pedido -->
    <div class="mb-6 bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-gray-700 font-medium">Estado del pedido:</span>
                <span class="px-4 py-2 rounded text-sm font-semibold {{ $pedido->entregado ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $pedido->entregado ? 'Entregado' : 'Pendiente' }}
                </span>
                @if($pedido->entregado && $pedido->fecha_entregado)
                <span class="text-sm text-gray-500">
                    Entregado el {{ $pedido->fecha_entregado->format('d/m/Y') }}
                </span>
                @endif
            </div>
            
            <form action="{{ route('admin.pedidos.toggleEntregado', $pedido->id) }}" method="POST">
                @csrf
                @method('PUT')
                <button 
                    type="submit"
                    class="px-4 py-2 {{ $pedido->entregado ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition-colors font-medium"
                >
                    {{ $pedido->entregado ? 'Marcar como Pendiente' : 'Marcar como Entregado' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Grid de información -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Información del Cliente -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-black text-white px-6 py-4">
                <h3 class="text-lg font-semibold">Información del Cliente</h3>
            </div>
            <div class="p-6 space-y-3">
                <div>
                    <span class="text-sm text-gray-500">Nombre</span>
                    <p class="font-medium text-gray-900">{{ $pedido->cliente->nombre }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Email</span>
                    <p class="font-medium text-gray-900">{{ $pedido->cliente->email }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Teléfono</span>
                    <p class="font-medium text-gray-900">{{ $pedido->cliente->telefono }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Domicilio</span>
                    <p class="font-medium text-gray-900">{{ $pedido->cliente->domicilio }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Localidad</span>
                    <p class="font-medium text-gray-900">{{ $pedido->cliente->localidad }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Provincia</span>
                    <p class="font-medium text-gray-900">{{ $pedido->cliente->provincia }}</p>
                </div>
            </div>
        </div>

        <!-- Información del Pedido -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-black text-white px-6 py-4">
                <h3 class="text-lg font-semibold">Información del Pedido</h3>
            </div>
            <div class="p-6 space-y-3">
                <div>
                    <span class="text-sm text-gray-500">Número de pedido</span>
                    <p class="font-medium text-gray-900">{{ $pedido->numero_pedido }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Fecha de compra</span>
                    <p class="font-medium text-gray-900">{{ $pedido->fecha_compra->format('d/m/Y') }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Fecha de entrega</span>
                    <div class="flex items-center gap-2 mt-1">
                        <form action="{{ route('admin.pedidos.updateFechaEntrega', $pedido->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <input 
                                type="date" 
                                name="fecha_entrega"
                                value="{{ $pedido->fecha_entrega->format('Y-m-d') }}"
                                class="border border-gray-300 rounded px-3 py-1 focus:ring-2 focus:ring-blue-500"
                            >
                            <button 
                                type="submit"
                                class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition-colors text-sm"
                            >
                                Actualizar
                            </button>
                        </form>
                    </div>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Forma de pago</span>
                    <p class="font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $pedido->forma_pago) }}</p>
                </div>
                
                @if($pedido->mensaje)
                <div class="pt-3 border-t">
                    <span class="text-sm text-gray-500">Mensaje del cliente</span>
                    <p class="font-medium text-gray-900 italic mt-1">{{ $pedido->mensaje }}</p>
                </div>
                @endif
                
                @if($pedido->archivo_path)
                <div class="pt-3 border-t">
                    <span class="text-sm text-gray-500">Archivo adjunto</span>
                    <a 
                        href="{{ asset('storage/' . $pedido->archivo_path) }}" 
                        target="_blank"
                        class="flex items-center gap-2 text-blue-600 hover:underline mt-1"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        {{ $pedido->archivo_nombre }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Productos -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="bg-black text-white px-6 py-4">
            <h3 class="text-lg font-semibold">Productos del Pedido</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pedido->items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->codigo_producto }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $item->nombre_producto }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            @if($item->descuento_unitario > 0)
                            <div class="text-gray-400 line-through text-xs">
                                ${{ number_format($item->precio_unitario, 2, ',', '.') }}
                            </div>
                            <div class="text-green-600 font-medium">
                                ${{ number_format($item->precio_unitario - $item->descuento_unitario, 2, ',', '.') }}
                            </div>
                            @else
                            <span class="text-gray-900">
                                ${{ number_format($item->precio_unitario, 2, ',', '.') }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                            {{ $item->cantidad }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                            ${{ number_format($item->subtotal, 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Totales -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="max-w-md ml-auto space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal sin descuento:</span>
                    <span class="font-medium text-gray-900">${{ number_format($pedido->subtotal_sin_descuento, 2, ',', '.') }}</span>
                </div>
                
                @if($pedido->descuentos > 0)
                <div class="flex justify-between text-sm text-green-600">
                    <span>Descuentos ({{ number_format($pedido->porcentaje_descuento, 2) }}%):</span>
                    <span class="font-medium">-${{ number_format($pedido->descuentos, 2, ',', '.') }}</span>
                </div>
                @endif
                
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium text-gray-900">${{ number_format($pedido->subtotal, 2, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">IVA ({{ number_format($pedido->porcentaje_iva, 2) }}%):</span>
                    <span class="font-medium text-gray-900">${{ number_format($pedido->iva, 2, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between text-xl font-bold border-t pt-3 mt-3">
                    <span class="text-gray-900">Total:</span>
                    <span class="text-gray-900">${{ number_format($pedido->total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection