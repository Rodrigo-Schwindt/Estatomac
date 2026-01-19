
<div class="max-w-[1224px] mx-auto pb-[88px]" x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0">
    <div class="bg-white pt-[24px] pb-[61px]">
        <nav class="max-w-[1224px] mx-auto text-black font-montserrat text-[12px] leading-[150%] flex items-center gap-1">
            <a wire:navigate href="{{ url('/') }}" class="text-black font-inter text-[12px] font-medium leading-normal">Inicio</a>
            <span class="text-black font-inter text-[12px] font-light leading-normal">›</span>
            <a wire:navigate href="{{ route('cliente.pedidos') }}" class="text-black font-inter text-[12px] font-medium leading-normal">Mis Pedidos</a>
            <span class="text-black font-inter text-[12px] font-light leading-normal">›</span>
            <span class="text-black font-inter text-[12px] font-light leading-normal">Pedido {{ $pedido->numero_pedido }}</span>
        </nav>
    </div>
    <!-- Header -->
    <div class="mb-[32px] flex justify-between items-center">
        <div>
            <h1 class="text-black font-inter text-[32px] font-semibold leading-normal">Pedido #{{ $pedido->numero_pedido }}</h1>
            <p class="text-black font-inter text-[14px] font-normal mt-1">Realizado el {{ $pedido->fecha_compra->format('d/m/Y') }}</p>
        </div>
        
        <div class="flex gap-[12px]">
            @if($pedido->descarga_habilitada)
                <button
                    wire:click="descargar"
                    class="rounded-[4px] bg-[#E40044] w-[200px] h-[44px] text-white text-center font-inter text-[14px] font-normal leading-normal uppercase flex items-center justify-center hover:bg-[#b8003a] transition-colors"
                >
                    DESCARGAR FACTURA
                </button>
            @else
                <button
                    disabled
                    title="La descarga no está habilitada aún"
                    class="rounded-[4px] bg-gray-300 w-[200px] h-[44px] text-gray-500 text-center font-inter text-[14px] font-normal leading-normal uppercase flex items-center justify-center cursor-not-allowed opacity-60"
                >
                    DESCARGA NO DISPONIBLE
                </button>
            @endif
            
            <a
                wire:navigate
                href="{{ route('cliente.pedidos') }}"
                class="rounded-[4px] border border-[#E40044] w-[150px] h-[44px] bg-white text-[#E40044] text-center font-inter text-[14px] font-normal leading-normal uppercase flex items-center justify-center hover:bg-[#E40044] hover:text-white transition-colors"
            >
                VOLVER
            </a>
        </div>
    </div>

    <!-- Estado del pedido -->
    <div class="mb-[32px] bg-white rounded-[6px] border border-[#E5E5E5] p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-black font-inter text-[16px] font-medium">Estado del pedido:</span>
                @if($pedido->entregado)
                    <span class="px-4 py-2 rounded bg-green-100 text-green-800 font-inter text-[14px] font-semibold">
                        Entregado
                    </span>
                    @if($pedido->fecha_entregado)
                    <span class="text-gray-500 font-inter text-[14px]">
                        el {{ $pedido->fecha_entregado->format('d/m/Y') }}
                    </span>
                    @endif
                @else
                    <span class="px-4 py-2 rounded bg-yellow-100 text-yellow-800 font-inter text-[14px] font-semibold">
                        Pendiente
                    </span>
                @endif
            </div>
            
            <div class="text-right">
                <div class="text-gray-500 font-inter text-[14px]">Fecha estimada de entrega</div>
                <div class="text-black font-inter text-[16px] font-semibold">{{ $pedido->fecha_entrega->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Grid de información -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-[24px] mb-[32px]">
        <!-- Información de Envío -->
        <div class="bg-white rounded-[6px] border border-[#E5E5E5] overflow-hidden">
            <div class="bg-black text-white px-[26px] h-[56px] flex items-center">
                <h3 class="font-inter text-[18px] font-semibold">Información de Envío</h3>
            </div>
            <div class="p-[26px] space-y-3">
                <div>
                    <span class="text-gray-500 font-inter text-[14px]">Nombre</span>
                    <p class="text-black font-inter text-[16px] font-medium">{{ $pedido->cliente->nombre }}</p>
                </div>
                <div>
                    <span class="text-gray-500 font-inter text-[14px]">Email</span>
                    <p class="text-black font-inter text-[16px] font-medium">{{ $pedido->cliente->email }}</p>
                </div>
                <div>
                    <span class="text-gray-500 font-inter text-[14px]">Teléfono</span>
                    <p class="text-black font-inter text-[16px] font-medium">{{ $pedido->cliente->telefono }}</p>
                </div>
                <div>
                    <span class="text-gray-500 font-inter text-[14px]">Domicilio</span>
                    <p class="text-black font-inter text-[16px] font-medium">{{ $pedido->cliente->domicilio }}</p>
                </div>
                <div>
                    <span class="text-gray-500 font-inter text-[14px]">Localidad</span>
                    <p class="text-black font-inter text-[16px] font-medium">{{ $pedido->cliente->localidad }}, {{ $pedido->cliente->provincia }}</p>
                </div>
            </div>
        </div>

        <!-- Información del Pedido -->
        <div class="bg-white rounded-[6px] border border-[#E5E5E5] overflow-hidden">
            <div class="bg-black text-white px-[26px] h-[56px] flex items-center">
                <h3 class="font-inter text-[18px] font-semibold">Información del Pedido</h3>
            </div>
            <div class="p-[26px] space-y-3">
                <div>
                    <span class="text-gray-500 font-inter text-[14px]">Número de pedido</span>
                    <p class="text-black font-inter text-[16px] font-medium">{{ $pedido->numero_pedido }}</p>
                </div>
                <div>
                    <span class="text-gray-500 font-inter text-[14px]">Fecha de compra</span>
                    <p class="text-black font-inter text-[16px] font-medium">{{ $pedido->fecha_compra->format('d/m/Y') }}</p>
                </div>
                <div>
                    <span class="text-gray-500 font-inter text-[14px]">Forma de pago</span>
                    <p class="text-black font-inter text-[16px] font-medium capitalize">{{ str_replace('_', ' ', $pedido->forma_pago) }}</p>
                </div>
                
                @if($pedido->mensaje)
                <div class="pt-3 border-t">
                    <span class="text-gray-500 font-inter text-[14px]">Tu mensaje</span>
                    <p class="text-black font-inter text-[16px] font-medium italic mt-1">{{ $pedido->mensaje }}</p>
                </div>
                @endif
                
                @if($pedido->archivo_path)
                <div class="pt-3 border-t">
                    <span class="text-gray-500 font-inter text-[14px]">Comprobante adjunto</span>
                    <a 
                        href="{{ asset('storage/' . $pedido->archivo_path) }}" 
                        target="_blank"
                        class="flex items-center gap-2 text-[#E40044] hover:underline mt-1"
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
    <div class="bg-white rounded-[6px] border border-[#E5E5E5] overflow-hidden mb-[32px]">
        <div class="bg-black text-white px-[26px] h-[56px] flex items-center">
            <h3 class="font-inter text-[18px] font-semibold">Productos</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-inter text-[14px] font-medium text-gray-500 uppercase">Código</th>
                        <th class="px-6 py-3 text-left font-inter text-[14px] font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-6 py-3 text-right font-inter text-[14px] font-medium text-gray-500 uppercase">Precio Unit.</th>
                        <th class="px-6 py-3 text-center font-inter text-[14px] font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-6 py-3 text-right font-inter text-[14px] font-medium text-gray-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach($pedido->items as $item)
                    <tr class="border-t border-gray-200">
                        <td class="px-6 py-4 font-inter text-[14px] text-gray-900">
                            {{ $item->codigo_producto }}
                        </td>
                        <td class="px-6 py-4 font-inter text-[14px] text-gray-900">
                            {{ $item->nombre_producto }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($item->descuento_unitario > 0)
                            <div class="text-gray-400 line-through font-inter text-[12px]">
                                ${{ number_format($item->precio_unitario, 2, ',', '.') }}
                            </div>
                            <div class="text-green-600 font-inter text-[14px] font-medium">
                                ${{ number_format($item->precio_unitario - $item->descuento_unitario, 2, ',', '.') }}
                            </div>
                            @else
                            <span class="text-gray-900 font-inter text-[14px]">
                                ${{ number_format($item->precio_unitario, 2, ',', '.') }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center font-inter text-[14px] text-gray-900">
                            {{ $item->cantidad }}
                        </td>
                        <td class="px-6 py-4 text-right font-inter text-[14px] font-semibold text-gray-900">
                            ${{ number_format($item->subtotal, 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Totales -->
    <div class="bg-white rounded-[6px] border border-[#E5E5E5] overflow-hidden">
        <div class="p-[26px]">
            <div class="max-w-md ml-auto space-y-3">
                <div class="flex justify-between font-inter text-[14px]">
                    <span class="text-gray-600">Subtotal sin descuento:</span>
                    <span class="text-gray-900 font-medium">${{ number_format($pedido->subtotal_sin_descuento, 2, ',', '.') }}</span>
                </div>
                
                @if($pedido->descuentos > 0)
                <div class="flex justify-between font-inter text-[14px] text-green-600">
                    <span>Descuentos ({{ number_format($pedido->porcentaje_descuento, 2) }}%):</span>
                    <span class="font-medium">-${{ number_format($pedido->descuentos, 2, ',', '.') }}</span>
                </div>
                @endif
                
                <div class="flex justify-between font-inter text-[14px]">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="text-gray-900 font-medium">${{ number_format($pedido->subtotal, 2, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between font-inter text-[14px]">
                    <span class="text-gray-600">IVA ({{ number_format($pedido->porcentaje_iva, 2) }}%):</span>
                    <span class="text-gray-900 font-medium">${{ number_format($pedido->iva, 2, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between font-inter text-[20px] font-bold border-t pt-3 mt-3">
                    <span class="text-gray-900">Total:</span>
                    <span class="text-gray-900">${{ number_format($pedido->total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>