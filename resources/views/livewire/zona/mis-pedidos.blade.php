<div class="max-w-[1224px] mx-auto pb-[88px] max-[1199px]:px-4 max-[1199px]:pb-16 max-[639px]:pb-12" x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0">
    <div class="bg-white pt-[24px] pb-[61px] max-[1199px]:pt-5 max-[1199px]:pb-10 max-[639px]:pt-4 max-[639px]:pb-8">
        <nav class="max-w-[1224px] mx-auto text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] flex items-center gap-1">
            <a wire:navigate href="{{ url('/') }}" class="text-black font-inter text-[12px] max-[639px]:text-[11px] font-medium leading-normal">Inicio</a>
            <span class="text-black font-inter text-[12px] max-[639px]:text-[11px] font-light leading-normal">|</span>
            <span class="text-black font-inter text-[12px] max-[639px]:text-[11px] font-light leading-normal">Mis Pedidos</span>
        </nav>
    </div>
    <h1 class="text-black font-inter text-[32px] max-[1199px]:text-[28px] max-[767px]:text-[24px] max-[639px]:text-[22px] font-semibold leading-normal mb-[32px] max-[1199px]:mb-6 max-[639px]:mb-5">Mis Pedidos</h1>

    {{-- AVISO CARRITO CON ÍTEMS --}}
    @if($carritoTieneItems)
    <div class="mb-4 flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-[6px] text-[13px] font-inter">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Tenés ítems en el carrito. Para anular pedidos primero debés vaciar o confirmar el carrito.
    </div>
    @endif

    {{-- BARRA DE ANULACIÓN --}}
    @if($hayPendientes)
    <div class="flex items-center justify-between mb-4 gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            @if($esVendedor)
            <span class="text-[13px] font-inter text-gray-500">Ordenar por:</span>
            <select wire:model.live="ordenar" class="border border-gray-200 rounded-[6px] px-3 py-1.5 font-inter text-[13px] focus:outline-none focus:ring-2 focus:ring-[#23378C]">
                <option value="fecha">Fecha</option>
                <option value="cliente">Cliente</option>
            </select>
            @endif
        </div>
        <button
            wire:click="anularSeleccionados"
            wire:confirm="¿Anular los pedidos seleccionados? Esta acción no se puede deshacer."
            @if(empty($seleccionados) || $carritoTieneItems) disabled @endif
            class="flex items-center gap-2 px-4 py-2 rounded-[6px] font-inter text-[14px] font-medium transition-colors
                   {{ empty($seleccionados) || $carritoTieneItems ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-red-600 text-white hover:bg-red-700' }}"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Anular seleccionados
            @if(!empty($seleccionados))
                <span class="bg-white text-red-600 text-[12px] font-bold rounded-full w-5 h-5 flex items-center justify-center">
                    {{ count($seleccionados) }}
                </span>
            @endif
        </button>
    </div>
    @endif

    @if($pedidos->isEmpty())
        <div class="bg-white rounded-[6px] p-12 max-[1199px]:p-8 max-[639px]:p-6 text-center animate-fadeIn">
            <svg class="w-24 h-24 max-[639px]:w-20 max-[639px]:h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 font-inter text-[18px] max-[639px]:text-[16px]">No tienes pedidos realizados</p>
            <a wire:navigate href="{{ route('cliente.productos') }}" class="inline-block mt-6 max-[639px]:mt-4 bg-[#23378C] text-white px-6 py-3 max-[639px]:px-5 max-[639px]:py-2.5 rounded-[40px] font-inter text-[14px] max-[639px]:text-[13px] font-semibold transition-colors hover:bg-[#1b2d72]">
                Ir a Productos
            </a>
        </div>
    @else
        <div class="max-[1199px]:hidden">
            <table class="w-full border-collapse rounded-t-[6px]">
                <thead class="rounded-t-[6px]">
                    <tr class="bg-[#f8f9f9] h-[44px] text-black font-inter text-[16px] leading-normal">
                        @if($hayPendientes)<th class="w-[44px]"></th>@endif
                        <th class="w-[80px]"></th>
                        <th class="text-left pl-[22px] font-semibold">Nro. de pedido</th>
                        <th class="text-left pl-[18px] font-semibold">Fecha de compra</th>
                        <th class="text-left pl-[18px] font-semibold">Detalle</th>
                        <th class="text-left pl-[18px] font-semibold">Fecha de entrega</th>
                        <th class="text-left pl-[10px] font-semibold">Importe</th>
                        <th class="text-center pl-[8px] font-semibold">Entregado</th>
                        <th class="text-right pr-[22px] font-semibold"></th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    <tr>
                        <td colspan="8" class="h-[18px]"></td>
                    </tr>

                    @foreach($pedidos as $pedido)
                        @php
                            $esPendiente = !$pedido->anulado && $pedido->enviado_erp_at === null;
                            $esAnulado   = $pedido->anulado;
                        @endphp
                        <tr class="{{ $esAnulado ? 'opacity-50' : '' }}">
                            @if($hayPendientes)
                            <td class="w-[44px] pl-3">
                                @if($esPendiente)
                                <input type="checkbox" wire:model.live="seleccionados" value="{{ $pedido->id }}"
                                       class="w-4 h-4 rounded border-gray-300 text-[#23378C] focus:ring-[#23378C] cursor-pointer">
                                @endif
                            </td>
                            @endif
                            <td class="w-[80px] h-[73px]">
                                <div class="flex items-center justify-center">
                                    <div class="w-[80px] h-[80px] flex items-center justify-center rounded border border-gray-200">
                                        <svg width="43" height="43" viewBox="0 0 43 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M28.667 7.16671H32.2503C33.2007 7.16671 34.1121 7.54424 34.7841 8.21624C35.4561 8.88825 35.8337 9.79968 35.8337 10.75V35.8334C35.8337 36.7837 35.4561 37.6952 34.7841 38.3672C34.1121 39.0392 33.2007 39.4167 32.2503 39.4167H10.7503C9.79997 39.4167 8.88853 39.0392 8.21653 38.3672C7.54452 37.6952 7.16699 36.7837 7.16699 35.8334V10.75C7.16699 9.79968 7.54452 8.88825 8.21653 8.21624C8.88853 7.54424 9.79997 7.16671 10.7503 7.16671H14.3337M21.5003 19.7084H28.667M21.5003 28.6667H28.667M14.3337 19.7084H14.3516M14.3337 28.6667H14.3516M16.1253 3.58337H26.8753C27.8648 3.58337 28.667 4.38553 28.667 5.37504V8.95837C28.667 9.94788 27.8648 10.75 26.8753 10.75H16.1253C15.1358 10.75 14.3337 9.94788 14.3337 8.95837V5.37504C14.3337 4.38553 15.1358 3.58337 16.1253 3.58337Z" stroke="#018637" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </td>

                            <td class="pl-[22px]">
                                <span class="text-black font-inter text-[16px] font-normal leading-[25px]">{{ $pedido->numero_pedido }}</span>
                                <span class="inline-block ml-2 px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $pedido->estado_badge['class'] }}">{{ $pedido->estado_badge['label'] }}</span>
                                @if($esVendedor && $pedido->cliente)
                                    <p class="text-[12px] text-[#23378C] font-inter font-medium mt-0.5 truncate max-w-[160px]">
                                        {{ $pedido->cliente->nombre }}
                                    </p>
                                @elseif(!$esVendedor && $pedido->vendedor)
                                    <p class="text-[12px] text-[#555] font-inter mt-0.5">
                                        por <span class="font-medium">{{ $pedido->vendedor->nombre }}</span>
                                    </p>
                                @endif
                            </td>

                            <td class="pl-[18px] text-black font-inter text-[16px] font-normal leading-normal">
                                {{ $pedido->fecha_compra->format('d/m/Y') }}
                            </td>

                            <td class="pl-[18px] text-black font-inter text-[14px] font-normal leading-normal w-[220px]">
                                @foreach($pedido->items as $item)
                                    <div class="truncate">{{ strip_tags($item->nombre_producto) }}</div>
                                @endforeach
                            </td>

                            <td class="pl-[18px] text-black font-inter text-[16px] font-normal leading-normal">
                                {{ $pedido->fecha_entrega ? $pedido->fecha_entrega->format('d/m/Y') : '-' }}
                            </td>

                            <td class="pl-[5px] text-black font-inter text-[16px] font-normal leading-normal">
                                ${{ number_format($pedido->total, 2, ',', '.') }}
                            </td>

                            <td class="text-center pl-[8px]">
                                <div class="flex items-center justify-center">
                                    @if($pedido->entregado)
                                        <div class="relative w-[38px] h-[38px] flex items-center justify-center">
                                            <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M37.5 0.5V37.5H0.5V0.5H37.5Z" fill="white" stroke="#D9D9D9"/>
                                                <path d="M36.9436 1.05554H1.05469V36.9444H36.9436V1.05554Z" stroke="#D9D9D9"/>
                                            </svg>
                                            <svg class="absolute" xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
                                                <path d="M8.83075 22.3135L0.380745 13.8635C-0.126915 13.3558 -0.126915 12.5327 0.380745 12.025L2.21918 10.1865C2.72684 9.67882 3.55 9.67882 4.05766 10.1865L9.74999 15.8788L21.9423 3.68653C22.45 3.17887 23.2731 3.17887 23.7808 3.68653L25.6192 5.52502C26.1269 6.03268 26.1269 6.85579 25.6192 7.3635L10.6692 22.3136C10.1615 22.8212 9.33841 22.8212 8.83075 22.3135Z" fill="#018637"/>
                                            </svg>
                                        </div>
                                    @else
                                        <svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M37.5 0.5V37.5H0.5V0.5H37.5Z" fill="white" stroke="#D9D9D9"/>
                                            <path d="M36.9436 1.05554H1.05469V36.9444H36.9436V1.05554Z" stroke="#D9D9D9"/>
                                        </svg>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div class="flex justify-end gap-[20px] pr-[22px]">
                                    <a
                                        href="{{ route('cliente.pedidos.detalle', $pedido->id) }}"
                                        wire:navigate
                                        class="rounded-[40px] border border-[#23378C] w-[127px] h-[40px] bg-white text-[#23378C] text-center font-inter text-[16px] font-semibold leading-normal flex items-center justify-center transition-colors"
                                    >
                                        Ver online
                                    </a>
                            
                                    @if($esVendedor)
                                        <a
                                            href="{{ route('cliente.pedidos.detalle', $pedido->id) }}"
                                            wire:navigate
                                            class="w-[127px] h-[40px] rounded-[40px] bg-[#23378C] text-white text-center font-inter text-[16px] font-semibold leading-normal flex items-center justify-center transition-colors"
                                        >
                                            Más detalles
                                        </a>
                                    @else
                                        <a
                                            href="{{ route('cliente.pedidos.descargar', $pedido->id) }}"
                                            class="w-[127px] h-[40px] rounded-[40px] bg-[#23378C] text-white text-center font-inter text-[16px] font-semibold leading-normal flex items-center justify-center transition-colors"
                                        >
                                            Recomprar
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="8" class="py-[10px]">
                                <div class="w-full h-[1px] bg-[#E5E5E5]"></div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="hidden max-[1199px]:grid gap-4">
            @foreach($pedidos as $index => $pedido)
                @php
                    $esPendiente = !$pedido->anulado && $pedido->enviado_erp_at === null;
                    $esAnulado   = $pedido->anulado;
                @endphp
                <article class="bg-white border border-[#E5E5E5] rounded-[6px] p-4 sm:p-5 transition-all duration-300 animate-fadeIn {{ $esAnulado ? 'opacity-50' : '' }}" style="animation-delay: {{ $index * 0.1 }}s;">
                    <div class="flex items-start gap-4">
                        <div class="w-[72px] h-[72px] flex-shrink-0 flex items-center justify-center rounded border border-gray-200">
                            <svg width="38" height="38" viewBox="0 0 43 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M28.667 7.16671H32.2503C33.2007 7.16671 34.1121 7.54424 34.7841 8.21624C35.4561 8.88825 35.8337 9.79968 35.8337 10.75V35.8334C35.8337 36.7837 35.4561 37.6952 34.7841 38.3672C34.1121 39.0392 33.2007 39.4167 32.2503 39.4167H10.7503C9.79997 39.4167 8.88853 39.0392 8.21653 38.3672C7.54452 37.6952 7.16699 36.7837 7.16699 35.8334V10.75C7.16699 9.79968 7.54452 8.88825 8.21653 8.21624C8.88853 7.54424 9.79997 7.16671 10.7503 7.16671H14.3337M21.5003 19.7084H28.667M21.5003 28.6667H28.667M14.3337 19.7084H14.3516M14.3337 28.6667H14.3516M16.1253 3.58337H26.8753C27.8648 3.58337 28.667 4.38553 28.667 5.37504V8.95837C28.667 9.94788 27.8648 10.75 26.8753 10.75H16.1253C15.1358 10.75 14.3337 9.94788 14.3337 8.95837V5.37504C14.3337 4.38553 15.1358 3.58337 16.1253 3.58337Z" stroke="#018637" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-[#6B6B6B] font-inter text-[13px] mb-1">Nro. de pedido</p>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-black font-inter text-[16px] font-semibold break-words">{{ $pedido->numero_pedido }}</h3>
                                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $pedido->estado_badge['class'] }}">{{ $pedido->estado_badge['label'] }}</span>
                                    </div>
                                    @if($esVendedor && $pedido->cliente)
                                        <p class="text-[12px] text-[#23378C] font-inter font-medium mt-0.5">
                                            {{ $pedido->cliente->nombre }}
                                        </p>
                                    @elseif(!$esVendedor && $pedido->vendedor)
                                        <p class="text-[12px] text-[#555] font-inter mt-0.5">
                                            por <span class="font-medium">{{ $pedido->vendedor->nombre }}</span>
                                        </p>
                                    @endif
                                </div>
                                <div class="flex-shrink-0">
                                    @if($pedido->entregado)
                                        <div class="relative w-[32px] h-[32px] flex items-center justify-center">
                                            <svg width="32" height="32" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M37.5 0.5V37.5H0.5V0.5H37.5Z" fill="white" stroke="#D9D9D9"/>
                                                <path d="M36.9436 1.05554H1.05469V36.9444H36.9436V1.05554Z" stroke="#D9D9D9"/>
                                            </svg>
                                            <svg class="absolute" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 26 26" fill="none">
                                                <path d="M8.83075 22.3135L0.380745 13.8635C-0.126915 13.3558 -0.126915 12.5327 0.380745 12.025L2.21918 10.1865C2.72684 9.67882 3.55 9.67882 4.05766 10.1865L9.74999 15.8788L21.9423 3.68653C22.45 3.17887 23.2731 3.17887 23.7808 3.68653L25.6192 5.52502C26.1269 6.03268 26.1269 6.85579 25.6192 7.3635L10.6692 22.3136C10.1615 22.8212 9.33841 22.8212 8.83075 22.3135Z" fill="#018637"/>
                                            </svg>
                                        </div>
                                    @else
                                        <svg width="32" height="32" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M37.5 0.5V37.5H0.5V0.5H37.5Z" fill="white" stroke="#D9D9D9"/>
                                            <path d="M36.9436 1.05554H1.05469V36.9444H36.9436V1.05554Z" stroke="#D9D9D9"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-[14px] font-inter leading-normal">
                                <div class="flex items-center gap-2">
                                    <span class="text-[#6B6B6B]">Compra</span>
                                    <span class="text-black">{{ $pedido->fecha_compra->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[#6B6B6B]">Entrega</span>
                                    <span class="text-black">{{ $pedido->fecha_entrega ? $pedido->fecha_entrega->format('d/m/Y') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 h-[1px] w-full bg-[#E5E5E5]"></div>

                    <div class="mt-4">
                        <p class="text-[#6B6B6B] font-inter text-[13px] mb-2">Detalle</p>
                        <div class="space-y-1">
                            @foreach($pedido->items as $item)
                                <p class="text-black font-inter text-[14px] leading-[1.45] break-words">{{ strip_tags($item->nombre_producto) }}</p>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-[#6B6B6B] font-inter text-[13px] mb-1">Importe</p>
                        <p class="text-black font-inter font-semibold text-[20px]">
                            ${{ number_format($pedido->total, 2, ',', '.') }}
                        </p>
                    </div>

                    @if($hayPendientes && $esPendiente)
                    <div class="mt-3 flex items-center gap-2">
                        <input type="checkbox" wire:model.live="seleccionados" value="{{ $pedido->id }}"
                               class="w-4 h-4 rounded border-gray-300 text-[#23378C] focus:ring-[#23378C] cursor-pointer">
                        <span class="font-inter text-[13px] text-gray-500">Seleccionar para anular</span>
                    </div>
                    @endif

                    <div class="mt-4 flex gap-3 pt-4 border-t border-[#E5E5E5] max-[639px]:flex-col">
                        <a
                            href="{{ route('cliente.pedidos.detalle', $pedido->id) }}"
                            wire:navigate
                            class="flex-1 rounded-[40px] border border-[#23378C] h-[40px] bg-white text-[#23378C] text-center font-inter text-[14px] font-semibold leading-normal flex items-center justify-center transition-colors"
                        >
                            Ver online
                        </a>

                        @if($esVendedor)
                            <a
                                href="{{ route('cliente.pedidos.detalle', $pedido->id) }}"
                                wire:navigate
                                class="flex-1 h-[40px] rounded-[40px] bg-[#23378C] text-white text-center font-inter text-[14px] font-semibold leading-normal flex items-center justify-center transition-colors"
                            >
                                Más detalles
                            </a>
                        @else
                            <a
                                href="{{ route('cliente.pedidos.descargar', $pedido->id) }}"
                                class="flex-1 h-[40px] rounded-[40px] bg-[#23378C] text-white text-center font-inter text-[14px] font-semibold leading-normal flex items-center justify-center transition-colors"
                            >
                                Recomprar
                            </a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
            opacity: 0;
        }
    </style>
</div>
