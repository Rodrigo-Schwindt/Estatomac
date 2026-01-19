<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Gestión de Pedidos</h1>
        
        <div class="flex gap-4">
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="buscar"
                    placeholder="Buscar por nº pedido o cliente..."
                    class="w-80 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <svg class="absolute right-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <select 
                wire:model.live="filtroEntregado"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
                <option value="todos">Todos</option>
                <option value="pendientes">Pendientes</option>
                <option value="entregados">Entregados</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-black text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Nº de pedido</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Fecha de compra</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Cliente</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Detalle</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Fecha de entrega</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold">Importe</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold">Entregado</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pedidos as $pedido)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900">{{ $pedido->numero_pedido }}</span>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600">{{ $pedido->fecha_compra->format('d/m/Y') }}</span>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900">{{ $pedido->cliente->nombre }}</div>
                            <div class="text-gray-500">{{ $pedido->cliente->email }}</div>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <button 
                            wire:click="abrirModal({{ $pedido->id }})"
                            type="button"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium underline"
                        >
                            Ver detalle de compra
                        </button>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input 
                            type="date" 
                            value="{{ $pedido->fecha_entrega->format('Y-m-d') }}"
                            wire:change="actualizarFechaEntrega({{ $pedido->id }}, $event.target.value)"
                            class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500"
                        >
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <span class="text-sm font-semibold text-gray-900">${{ number_format($pedido->total, 2, ',', '.') }}</span>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <label class="flex items-center justify-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                wire:click="toggleEntregado({{ $pedido->id }})"
                                @checked($pedido->entregado)
                                class="w-6 h-6 text-green-600 border-gray-300 rounded focus:ring-green-500 cursor-pointer"
                            >
                        </label>
                        @if($pedido->entregado && $pedido->fecha_entregado)
                        <div class="text-xs text-gray-500 mt-1">{{ $pedido->fecha_entregado->format('d/m/Y') }}</div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        No se encontraron pedidos
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $pedidos->links() }}
    </div>

    <!-- Modal -->
    @if($modalDetalle && $pedidoSeleccionado)
    <div 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        wire:click.self="cerrarModal"
    >
        <div class="bg-white rounded-lg shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col" wire:click.stop>
            <!-- Header del modal -->
            <div class="bg-black text-white px-6 py-4 flex justify-between items-center rounded-t-lg flex-shrink-0">
                <h2 class="text-xl font-semibold">Detalle del Pedido #{{ $pedidoSeleccionado->numero_pedido }}</h2>
                <button 
                    wire:click="cerrarModal"
                    type="button"
                    class="text-white hover:text-gray-300 text-3xl leading-none"
                >
                    ×
                </button>
            </div>

            <!-- Contenido del modal con scroll -->
            <div class="overflow-y-auto flex-1 p-6">
                <div class="space-y-6">
                    <!-- Información del cliente y pedido -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3 text-gray-900">Información del Cliente</h3>
                            <div class="space-y-2 text-sm bg-gray-50 p-4 rounded">
                                <p><span class="font-medium">Nombre:</span> {{ $pedidoSeleccionado->cliente->nombre }}</p>
                                <p><span class="font-medium">Email:</span> {{ $pedidoSeleccionado->cliente->email }}</p>
                                <p><span class="font-medium">Teléfono:</span> {{ $pedidoSeleccionado->cliente->telefono }}</p>
                                <p><span class="font-medium">Domicilio:</span> {{ $pedidoSeleccionado->cliente->domicilio }}</p>
                                <p><span class="font-medium">Localidad:</span> {{ $pedidoSeleccionado->cliente->localidad }}</p>
                                <p><span class="font-medium">Provincia:</span> {{ $pedidoSeleccionado->cliente->provincia }}</p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-3 text-gray-900">Información del Pedido</h3>
                            <div class="space-y-2 text-sm bg-gray-50 p-4 rounded">
                                <p><span class="font-medium">Fecha de compra:</span> {{ $pedidoSeleccionado->fecha_compra->format('d/m/Y') }}</p>
                                <p><span class="font-medium">Fecha de entrega:</span> {{ $pedidoSeleccionado->fecha_entrega->format('d/m/Y') }}</p>
                                <p><span class="font-medium">Forma de pago:</span> <span class="capitalize">{{ str_replace('_', ' ', $pedidoSeleccionado->forma_pago) }}</span></p>
                                <p><span class="font-medium">Estado:</span> 
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $pedidoSeleccionado->entregado ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $pedidoSeleccionado->entregado ? 'Entregado' : 'Pendiente' }}
                                    </span>
                                </p>
                                @if($pedidoSeleccionado->mensaje)
                                <div class="mt-3 pt-3 border-t">
                                    <p class="font-medium">Mensaje:</p>
                                    <p class="text-gray-600 italic mt-1">{{ $pedidoSeleccionado->mensaje }}</p>
                                </div>
                                @endif
                                @if($pedidoSeleccionado->archivo_path)
                                <div class="mt-3 pt-3 border-t">
                                    <p class="font-medium">Archivo adjunto:</p>
                                    <a 
                                        href="{{ asset('storage/' . $pedidoSeleccionado->archivo_path) }}" 
                                        target="_blank"
                                        class="text-blue-600 hover:underline mt-1 inline-block"
                                    >
                                        📎 {{ $pedidoSeleccionado->archivo_nombre }}
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Productos -->
                    <div>
                        <h3 class="text-lg font-semibold mb-3 text-gray-900">Productos</h3>
                        <div class="border rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pedidoSeleccionado->items as $item)
                                        <tr>
                                            <td class="px-4 py-3 text-sm">{{ $item->codigo_producto }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $item->nombre_producto }}</td>
                                            <td class="px-4 py-3 text-sm text-right">
                                                @if($item->descuento_unitario > 0)
                                                <div class="text-gray-400 line-through text-xs">${{ number_format($item->precio_unitario, 2, ',', '.') }}</div>
                                                <div class="text-green-600 font-medium">${{ number_format($item->precio_unitario - $item->descuento_unitario, 2, ',', '.') }}</div>
                                                @else
                                                ${{ number_format($item->precio_unitario, 2, ',', '.') }}
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-center">{{ $item->cantidad }}</td>
                                            <td class="px-4 py-3 text-sm text-right font-medium">${{ number_format($item->subtotal, 2, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Totales -->
                    <div class="border-t pt-4">
                        <div class="max-w-md ml-auto space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Subtotal sin descuento:</span>
                                <span class="font-medium">${{ number_format($pedidoSeleccionado->subtotal_sin_descuento, 2, ',', '.') }}</span>
                            </div>
                            @if($pedidoSeleccionado->descuentos > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Descuentos ({{ number_format($pedidoSeleccionado->porcentaje_descuento, 2) }}%):</span>
                                <span class="font-medium">-${{ number_format($pedidoSeleccionado->descuentos, 2, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span class="font-medium">${{ number_format($pedidoSeleccionado->subtotal, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>IVA ({{ number_format($pedidoSeleccionado->porcentaje_iva, 2) }}%):</span>
                                <span class="font-medium">${{ number_format($pedidoSeleccionado->iva, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t pt-2 mt-2">
                                <span>Total:</span>
                                <span>${{ number_format($pedidoSeleccionado->total, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer del modal -->
            <div class="bg-gray-50 px-6 py-4 border-t rounded-b-lg flex gap-3 flex-shrink-0">
                <a 
                    href="{{ route('admin.pedidos.factura', $pedidoSeleccionado->id) }}"
                    target="_blank"
                    class="flex-1 bg-green-600 text-white py-3 px-4 rounded hover:bg-green-700 transition-colors font-medium text-center"
                >
                    📄 Generar Factura PDF
                </a>
                <button 
                    wire:click="cerrarModal"
                    type="button"
                    class="flex-1 bg-black text-white py-3 px-4 rounded hover:bg-gray-800 transition-colors"
                >
                    Cerrar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>