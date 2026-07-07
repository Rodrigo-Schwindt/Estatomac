<div x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0">
    <div class="bg-white pt-[24px] pb-[61px]">
        <nav class="max-w-[1224px] mx-auto px-4 sm:px-6 min-[1200px]:px-0 text-black font-montserrat text-[12px] leading-[150%] flex items-center gap-1 overflow-x-auto scrollbar-hide">
            <a wire:navigate href="{{ url('/') }}" class="text-black font-inter text-[12px] font-medium leading-normal whitespace-nowrap">Inicio</a>
            <span class="text-black font-inter text-[12px] font-light leading-normal">|</span>
            <span class="text-black font-inter text-[12px] font-light leading-normal whitespace-nowrap">Carrito</span>
        </nav>
    </div>

    @if($esVendedor)
    <div class="max-w-[1224px] mx-auto px-4 sm:px-6 min-[1200px]:px-0 mb-4">
        @if($clienteNombre)
            <div class="flex items-center gap-3 bg-[#EEF2FF] border border-[#23378C]/20 rounded-lg px-4 py-3">
                <svg class="w-5 h-5 text-[#23378C] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="font-inter text-[14px] text-[#23378C]">
                    Carrito de <span class="font-semibold">{{ $clienteNombre }}</span>
                </span>
                <a href="{{ route('cliente.productos') }}" class="ml-auto font-inter text-[13px] text-[#23378C] hover:underline">
                    ← Volver a productos
                </a>
            </div>
        @else
            <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <span class="font-inter text-[14px] text-amber-700">
                    No hay cliente seleccionado. <a href="{{ route('cliente.productos') }}" class="font-semibold underline">Seleccioná un cliente</a> para ver su carrito.
                </span>
            </div>
        @endif
    </div>
    @endif

    <div class="max-w-[1224px] mx-auto px-4 sm:px-6 min-[1200px]:px-0 pb-[88px]"
         x-data="{ animate: false }"
         x-init="setTimeout(() => animate = true, 100)"
         x-show="animate"
         x-transition:enter="transition ease-out duration-400"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0">
        <div class="rounded-t-[4px] max-[1199px]:overflow-x-auto max-[1199px]:pb-3">
            <table class="table-fixed w-full border-collapse max-[1199px]:min-w-[1224px]">
                <colgroup>
                    <col style="width:78px">
                    <col style="width:88px">
                    <col style="width:62px">
                    <col style="width:88px">
                    <col>{{-- nombre: ocupa el resto --}}
                    <col style="width:108px">
                    <col style="width:72px">
                    <col style="width:52px">
                    <col style="width:82px">
                    <col style="width:46px">
                    <col style="width:88px">
                    <col style="width:70px">
                    <col style="width:80px">
                    <col style="width:76px">
                </colgroup>
                <thead>
                    <tr class="bg-[#f8f8f8] h-[48px] text-[16px] text-[#131313] font-inter font-semibold leading-[100%]">
                        <th class="px-2 text-left"></th>
                        <th class="px-2 text-left pl-10">Codigo</th>
                        <th class="px-2 text-left pl-4">Cod. Color</th>
                        <th class="px-2 text-left">Categoria</th>
                        <th class="px-2 text-left w-[130px]">Nombre</th>
                        <th class="px-2 text-left">Presentacion</th>
                        <th class="px-2 text-center">Precio</th>
                        <th class="px-2 text-center pl-6">Desc.</th>
                        <th class="px-2 text-right">Precio c/<br>desc.</th>
                        <th class="px-2 text-center">Bulto</th>
                        <th class="px-2 text-center">Cant. x Pres.</th>
                        <th class="px-2 text-center">Cantidad x Bulto</th>
                        <th class="px-2 text-right">Subtotal</th>
                        <th class="px-2 text-center"></th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    <tr><td colspan="14" class="h-[10px]"></td></tr>

                    @foreach($items as $item)
                        @php
                            $portada             = $item->productoTodotex?->gallery->firstWhere('portada', true) ?? $item->productoTodotex?->gallery->first();
                            $familia             = $item->productoTodotex?->categorias->first()?->familia;
                            $precioOriginal      = floatval($item->precio_unitario);
                            $descuentoPct        = floatval($item->descuento_unitario ?? 0);
                            $descuentoPersonalizadoPct = floatval($item->descuento_personalizado ?? 0);
                            $precioDesc          = $precioOriginal * (1 - $descuentoPct / 100) * (1 - $descuentoPersonalizadoPct / 100);
                            $bultoRaw            = trim((string) ($item->productoTodotex?->bulto ?? ''));
                            $bulto               = $bultoRaw !== '' ? $bultoRaw : '1';
                            $bultoCantidad       = max(1, intval($item->productoTodotex?->bulto_cantidad ?? 1));
                            $cantidadPresentacion = max(0, intval($item->cantidad_presentacion ?? $item->cantidad ?? 0));
                            $cantidadBultos      = max(0, intval($item->cantidad_bultos ?? 0));
                            $cantidadTotal       = $cantidadPresentacion + ($cantidadBultos * $bultoCantidad);
                            $subtotal            = $precioDesc * $cantidadTotal;
                        @endphp

                        <tr wire:key="cart-row-{{ $item->id }}" class="align-middle border-b border-[#F0F0F0] hover:bg-[#FAFAFA] transition-colors">
                            {{-- Foto --}}
                            <td class="px-2 py-2">
                                <div class="w-[80px] h-[80px] relative rounded-[4px] flex items-center justify-center overflow-hidden">
                                    @if($portada)
                                        <img src="{{ Storage::url($portada->image) }}" alt="{{ $item->productoTodotex?->codigo }}" class="w-full h-full object-cover p-0.5">
                                    @else
                                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    @endif
                                    <div class="absolute inset-0 bg-gray-400/10 pointer-events-none"></div>
                                </div>
                            </td>

                            <td class="px-2 py-2 text-black font-inter text-[14px] pl-12 font-semibold uppercase">
                                {{ $item->productoTodotex?->codigo ?? '-' }}
                            </td>

                            <td class="px-2 py-2 text-black font-inter text-[14px] text-center">
                                {{ $item->productoTodotex?->codigo_color ?? '-' }}
                            </td>

                            <td class="px-2 py-2 text-black font-inter text-[14px] font-medium">
                                <span class="line-clamp-2">{{ $familia?->titulo ?? '-' }}</span>
                            </td>

                            <td class="px-2 py-2 text-black font-inter text-[14px] w-[130px]">
                                <span class="line-clamp-3">{{ strip_tags($item->productoTodotex?->descripcion ?? '-') }}</span>
                            </td>

                            <td class="px-2 py-2 text-[#111] font-inter text-[14px]">
                                <span class="line-clamp-2">{{ strip_tags($item->productoTodotex?->presentacion ?? '-') }}</span>
                            </td>

                            <td class="px-2 py-2 text-black font-inter text-[15px] text-center font-bold">
                                ${{ number_format($precioOriginal, 2, ',', '.') }}
                            </td>

                            {{-- Descuento --}}
                            <td class="px-2 py-2 text-center pl-6">
                                @if($descuentoPct > 0)
                                    <span class="inline-block text-[#308C05] font-inter text-[14px] font-semibold px-1.5 py-0.5 rounded-full">
                                        {{ number_format($descuentoPct, 0) }}%
                                    </span>
                                @else
                                    <span class="text-[#aaa] text-[11px]">-</span>
                                @endif
                                @if($esVendedor)
                                    <div wire:key="desc-pers-cart-{{ $item->id }}"
                                         x-data="{
                                            val: {{ (float) $descuentoPersonalizadoPct }},
                                            save() { $wire.setDescuentoPersonalizado({{ $item->id }}, this.val || 0); }
                                         }"
                                         class="mt-1 flex flex-col items-center gap-0.5">
                                        <input type="number"
                                               x-model.number="val"
                                               @blur="save()"
                                               @keydown.enter.prevent="save()"
                                               min="0" max="100" step="0.5"
                                               placeholder="+%"
                                               title="Descuento personalizado"
                                               class="w-[40px] h-[22px] text-center border border-[#D7E8DC] rounded text-[11px] font-semibold text-[#308C05] py-0.5 focus:outline-none focus:ring-1 focus:ring-[#018637] [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                        <span class="font-inter text-[9px] leading-none text-[#777]">pers.</span>
                                    </div>
                                @elseif($descuentoPersonalizadoPct > 0)
                                    <span class="mt-1 block text-[#308C05] font-inter text-[11px] font-semibold leading-none">
                                        +{{ number_format($descuentoPersonalizadoPct, 0) }}% pers.
                                    </span>
                                @endif
                            </td>

                            {{-- Precio c/desc --}}
                            <td class="px-2 py-2 text-[#111] text-[15px] font-bold text-right">
                                ${{ number_format($precioDesc, 2, ',', '.') }}
                            </td>

                            {{-- Bulto --}}
                            <td class="px-2 py-2 text-black font-inter text-[14px] text-center">
                                {{ $bulto }}
                            </td>

                            {{-- Cant. x Presentacion (contador) --}}
                            <td class="px-1 py-2 text-center">
                                <div class="inline-flex items-center border border-[#E8E8E8] rounded-[4px] h-[44px] w-[64px] bg-white">
                                    <button type="button"
                                            wire:click="decrementar({{ $item->id }})"
                                            class="text-[#111] font-inter text-[18px] font-normal leading-none w-5 flex items-center justify-center hover:text-black select-none">
                                        -
                                    </button>
                                    <span class="font-inter text-[16px] font-medium text-[#1a1a1a] w-6 text-center tabular-nums">
                                        {{ $cantidadPresentacion }}
                                    </span>
                                    <button type="button"
                                            wire:click="incrementar({{ $item->id }})"
                                            class="text-[#111] font-inter text-[18px] font-normal leading-none w-5 flex items-center justify-center hover:text-black select-none">
                                        +
                                    </button>
                                </div>
                            </td>

                            {{-- Cantidad x Bulto --}}
                            <td class="px-1 py-2 text-center">
                                <div class="inline-flex items-center border border-[#E8E8E8] rounded-[4px] h-[44px] w-[64px] bg-white">
                                    <button type="button"
                                            wire:click="decrementarBulto({{ $item->id }})"
                                            class="text-[#111] font-inter text-[18px] font-normal leading-none w-5 flex items-center justify-center hover:text-black select-none">
                                        -
                                    </button>
                                    <span class="font-inter text-[16px] font-medium text-[#1a1a1a] w-6 text-center tabular-nums">
                                        {{ $cantidadBultos }}
                                    </span>
                                    <button type="button"
                                            wire:click="incrementarBulto({{ $item->id }})"
                                            class="text-[#111] font-inter text-[18px] font-normal leading-none w-5 flex items-center justify-center hover:text-black select-none">
                                        +
                                    </button>
                                </div>
                            </td>

                            {{-- Subtotal --}}
                            <td class="px-2 py-2 text-black font-inter text-[15px] font-bold text-right">
                                ${{ number_format($subtotal, 2, ',', '.') }}
                            </td>

                            {{-- Eliminar --}}
                            <td class="px-2 py-2 text-center">
                                <button
                                    wire:click="eliminar({{ $item->id }})"
                                    wire:confirm="Estas seguro de eliminar este producto?"
                                    class="rounded-[4px] cursor-pointer border-1 border-[#23378C] w-[40px] h-[40px] text-[#23378C] flex items-center justify-center hover:bg-[#23378C] hover:text-white transition-colors mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <path d="M6.28531 8.571L7.42831 20H16.5703L17.7133 8.571M13.4993 15.5V10.5M10.4993 15.5V10.5M4.57031 6.286H9.14231M9.14231 6.286L9.52431 4.757C9.57848 4.54075 9.70336 4.34881 9.8791 4.21166C10.0548 4.0745 10.2714 4.00001 10.4943 4H13.5043C13.7272 4.00001 13.9438 4.0745 14.1195 4.21166C14.2953 4.34881 14.4201 4.54075 14.4743 4.757L14.8563 6.286M9.14231 6.286H14.8563M14.8563 6.286H19.4283" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-[32px] min-[1200px]:mt-[44px]">
            <a 
                wire:navigate 
                href="{{ route('cliente.productos') }}"
                class="inline-flex items-center w-full min-[480px]:w-[231px] h-[40px] bg-transparent text-[#23378C] text-center font-inter text-[16px] sm:text-[16px] font-semibold leading-normal rounded-[40px] border border-[#23378C] justify-center hover:bg-[#E40044] hover:text-white transition-colors">
                <span>+ </span>Agregar mas productos
            </a>
        </div>

        <form action="{{ route('cliente.carrito.realizar-pedido') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="forma_entrega" :value="$wire.formEntrega">
            
            <div class="mt-[48px] grid grid-cols-1 gap-[24px] sm:mt-[72px] min-[1200px]:mt-[123px] min-[1200px]:grid-cols-2">
                <div class="space-y-[24px]">
                    <div class="border border-gray-200">
                        <div class="bg-white text-black px-[20px] sm:px-[26px] h-[56px] flex items-center rounded-t-[4px]">
                            <h3 class="font-semibold  border-b border-gray-200 text-[20px] pt-[18px] w-full pb-[15px] sm:text-[18px] font-semibold">Informacion importante</h3>
                        </div>
                        <div class="bg-white  px-[20px] sm:px-[28px] pt-[20px] sm:pt-[29px] pb-[24px] sm:pb-[31px] rounded-b-[4px]">
                            @if($config && $config->informacion)
                                <div class="prose prose-sm max-w-none text-[14px] sm:text-[16px]">
                                    {!! $config->informacion !!}
                                </div>
                            @else
                                <p class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-[25px]">- Venta sujeta a disponibilidad en stock</p>
                                <p class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-[25px]">- Los precios se encuentran expresados en pesos</p>
                                <p class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-[25px]">- El plazo de entrega se coordina con la empresa</p>
                            @endif
                        </div>
                    </div>
        
                    <div class="border border-gray-200">
                        <div class="bg-white text-black px-[20px] sm:px-[26px] h-[56px] flex items-center rounded-t-[4px]">
                            <h3 class="font-semibold border-b border-gray-200 text-[20px] pt-[18px] w-full pb-[15px] sm:text-[18px] font-semibold">Escribinos un mensaje</h3>
                        </div>
                        <div class="bg-white px-[20px] sm:px-[27px] pt-[14px] pb-[40px] sm:pb-[61px] rounded-b-[4px]">
                            <textarea
                                name="mensaje"
                                rows="4"
                                placeholder="{{ $config && $config->escribenos ? strip_tags($config->escribenos) : 'Días especiales de entrega, cambios de domicilio, expresos, requerimientos especiales en la mercadería, exenciones...' }}"
                                class="w-full bg-transparent border-none px-[16px] py-[12px] text-[14px] sm:text-[16px] font-inter text-black placeholder-gray-400 focus:outline-none resize-none"
                            ></textarea>
                        </div>
                    </div>
        
                    <div>
                        <h3 class="text-[#151515] font-semibold text-[20px] sm:text-[20px] font-semibold leading-normal mb-[15px] ">Adjunta un archivo</h3>
                        <div class="flex flex-col sm:flex-row w-full gap-2 sm:gap-0">
                            <div class="bg-white border border-gray-200 px-[20px] sm:px-[24px] w-full h-[48px] flex items-center rounded-[4px] sm:rounded-r-none">
                                <div class="flex items-center gap-4">
                                    <input 
                                        type="file" 
                                        name="archivo"
                                        id="archivo" 
                                        class="hidden"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        onchange="document.getElementById('archivo-nombre').textContent = this.files[0]?.name || 'Seleccionar archivo'"
                                    >
                                    <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal truncate" id="archivo-nombre">
                                        Seleccionar archivo
                                    </span>
                                </div>
                            </div>
                            <label 
                                for="archivo" 
                                class="bg-[#23378C] text-white w-full sm:w-[135px] h-[48px] flex items-center justify-center rounded-[4px] sm:rounded-l-none cursor-pointer text-[14px] sm:text-[14px] font-medium  hover:bg-gray-800 transition-colors">
                                Adjuntar
                            </label>
                        </div>
                    </div>
                </div>
        
                <div>
                    @if(count($opcionesEntrega) > 0)
                    <div class="border border-gray-200 mb-[26px]">
                        <div class="bg-white text-black px-[20px] sm:px-[26px] h-[56px] flex items-center rounded-t-[4px]">
                            <h3 class="font-semibold border-b border-gray-200 text-[20px] pt-[18px] w-full pb-[15px] sm:text-[18px] font-semibold">Entrega</h3>
                        </div>
                        <div class="bg-white px-[20px] sm:px-[24px] py-[18px] rounded-b-[4px] space-y-3">
                            @foreach($opcionesEntrega as $valor => $opcion)
                            <label class="flex cursor-pointer items-start justify-between gap-3 sm:items-center">
                                <div class="flex items-start gap-[14px] sm:items-center sm:gap-[21px]">
                                    <input
                                        type="radio"
                                        name="forma_entrega"
                                        value="{{ $valor }}"
                                        wire:model.live="formEntrega"
                                        class="w-[18px] h-[18px] sm:w-[20px] sm:h-[20px] text-[#23378C] focus:ring-[#23378C] flex-shrink-0"
                                        {{ $loop->first ? 'required' : '' }}
                                    >
                                    <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">{{ $opcion['label'] }}</span>
                                </div>
                                @if($opcion['costo'] > 0)
                                    <span class="ml-2 text-right font-inter text-[13px] sm:text-[16px] font-normal leading-normal text-black whitespace-nowrap">${{ number_format($opcion['costo'], 2, ',', '.') }}</span>
                                @else
                                    <span class="ml-2 text-right font-inter text-[13px] sm:text-[16px] font-normal leading-normal text-[#308C05] whitespace-nowrap">Sin costo</span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(count($opcionesPago) > 0)
                    <div class="border border-gray-200 mb-[26px]">
                        <div class="bg-white text-black px-[20px] sm:px-[26px] h-[56px] flex items-center rounded-t-[4px]">
                            <h3 class="font-semibold border-b border-gray-200 text-[20px] pt-[18px] w-full pb-[15px] sm:text-[18px] font-semibold">Formas de pago</h3>
                        </div>
                        <div class="bg-white px-[20px] sm:px-[24px] py-[18px] rounded-b-[4px] space-y-3">
                            @foreach($opcionesPago as $valor => $opcion)
                            <label class="flex cursor-pointer items-start justify-between gap-3 sm:items-center">
                                <div class="flex items-start gap-[14px] sm:items-center sm:gap-[21px]">
                                    <input
                                        type="radio"
                                        name="forma_pago"
                                        value="{{ $valor }}"
                                        wire:model.live="formaPago"
                                        class="w-[18px] h-[18px] sm:w-[20px] sm:h-[20px] text-[#E4002B] focus:ring-[#E40044] flex-shrink-0"
                                        {{ $loop->first ? 'required' : '' }}
                                    >
                                    <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">{{ $opcion['label'] }}</span>
                                </div>
                                @if($opcion['descuento'] > 0)
                                    <span class="text-[#308C05] text-right font-inter text-[13px] sm:text-[16px] font-normal leading-normal whitespace-nowrap ml-2">{{ rtrim(rtrim(number_format($opcion['descuento'], 2, '.', ''), '0'), '.') }}% descuento</span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="border border-gray-200 mb-[26px]">
                    <div class="bg-white text-black px-[20px] sm:px-[26px] h-[56px] flex items-center rounded-t-[4px] ">
                        <h3 class="font-semibold border-b border-gray-200 text-[20px] pt-[18px] w-full pb-[15px]  sm:text-[18px] font-semibold">Tu pedido</h3>
                    </div>
                    <div class="bg-white  px-4 sm:px-6 pt-[18px] rounded-b-[4px] space-y-4 pb-[12px]">
                
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">Subtotal sin descuento</span>
                            <span class="text-black text-right font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">${{ number_format($subtotalSinDescuento, 2, ',', '.') }}</span>
                        </div>
                    
                        @if($descuentoCliente > 0)
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-[#308C05] font-inter text-[14px] sm:text-[16px] font-normal leading-normal">Descuento cliente</span>
                            <span class="text-[#308C05] font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">-${{ number_format($descuentoCliente, 2, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($descuentoPersonalizado > 0)
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-[#308C05] font-inter text-[14px] sm:text-[16px] font-normal leading-normal">Descuento adicional</span>
                            <span class="text-[#308C05] font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">-${{ number_format($descuentoPersonalizado, 2, ',', '.') }}</span>
                        </div>
                        @endif
                
                        @if($descuentoPorPago > 0)
                        @php
                            $porcentajePago = $opcionesPago[$formaPago]['descuento'] ?? 0;
                        @endphp
                        <div class="flex justify-between items-center text-[#007600] gap-2">
                            <span class="text-[#308C05] font-inter text-[14px] sm:text-[16px] font-normal leading-normal">
                                Descuento {{ ucfirst(str_replace('_', ' ', $formaPago)) }} ({{ rtrim(rtrim(number_format($porcentajePago, 2, '.', ''), '0'), '.') }}%)
                            </span>
                            <span class="text-[#308C05] font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">-${{ number_format($descuentoPorPago, 2, ',', '.') }}</span>
                        </div>
                        @endif
                    
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">Subtotal con descuentos</span>
                            <span class="text-black text-right font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">${{ number_format($subtotalConDescuentoPago, 2, ',', '.') }}</span>
                        </div>
                    
                        @if($costoEntrega > 0)
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">Entrega</span>
                            <span class="text-black text-right font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">${{ number_format($costoEntrega, 2, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-black font-inter text-[14px] sm:text-[16px] font-normal leading-normal">IVA {{ $config ? rtrim(rtrim(number_format($config->iva, 2, '.', ''), '0'), '.') : '21' }}%</span>
                                <span class="text-black text-right font-inter text-[14px] sm:text-[16px] font-normal leading-normal whitespace-nowrap">${{ number_format($iva, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    
                        <div>
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-1 sm:gap-2">
                                <div class="flex flex-col sm:flex-row gap-1 sm:gap-2 items-start sm:items-center">
                                    <span class="text-black font-inter text-[20px] sm:text-[24px] font-semibold leading-normal">Total</span>
                                    <span class="text-black font-inter text-[13px] sm:text-[15px] font-semibold leading-normal sm:pt-1">(IVA incluido)</span>
                                </div>
                                <span class="text-black text-right font-inter text-[20px] sm:text-[24px] font-semibold leading-normal whitespace-nowrap">${{ number_format($total, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    
                    </div>
                </div>
                
                    <div class="flex flex-col gap-4 pt-4 min-[1200px]:flex-row">
                        <a
                            href="{{ route('cliente.productos') }}"
                            class="w-full h-[40px] cursor-pointer rounded-[40px] border border-[#23378C] text-[#23378C] text-center font-inter text-[16px] sm:text-[16px] font-normal leading-normal bg-transparent flex items-center justify-center hover:bg-[#E40044] hover:text-white transition-colors">
                            Cancelar
                        </a>
                        <button
                            type="submit"
                            class="w-full rounded-[40px] cursor-pointer bg-[#23378C] text-white text-center font-inter text-[16px] sm:text-[16px] font-normal leading-normal h-[40px] hover:bg-[#016d2e] transition-colors">
                            Realizar pedido
                        </button>
                    </div>
                </div>
            </div>
        </form>


    </div>

    @if(session('success') || session('error'))
    <div 
        x-data="{ 
            show: false, 
            message: @json(session('success') ?? session('error') ?? ''),
            type: '{{ session('success') ? 'success' : 'error' }}',
            progress: 100,
            progressInterval: null,
            init() {
                setTimeout(() => {
                    this.show = true;
                    this.animarProgreso();
                }, 100);
            },
            animarProgreso() {
                this.progress = 100;
                if (this.progressInterval) clearInterval(this.progressInterval);
                this.progressInterval = setInterval(() => {
                    this.progress -= 3.33;
                    if (this.progress <= 0) {
                        clearInterval(this.progressInterval);
                    }
                }, 100);
                
                setTimeout(() => { 
                    this.show = false;
                    clearInterval(this.progressInterval);
                }, 3000);
            }
        }"
        x-show="show"
        x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="opacity-0 scale-90 -translate-y-8"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="fixed left-1/2 z-[100] w-[calc(100vw-32px)] max-w-[480px] -translate-x-1/2 px-4 sm:w-auto"
        style="top: 104px;"
    >
        <div class="relative overflow-hidden rounded-2xl border border-white/50 bg-gradient-to-br from-white to-gray-50 shadow-[0_20px_60px_rgba(0,0,0,0.15)] backdrop-blur-xl sm:min-w-[380px]">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gray-200 to-gray-300">
                <div 
                    class="h-full transition-all duration-100 ease-linear rounded-r-full"
                    :class="{
                        'bg-gradient-to-r from-[#00C853] via-[#00E676] to-[#69F0AE]': type === 'success',
                        'bg-gradient-to-r from-[#E40044] via-[#FF1744] to-[#F50057]': type === 'error'
                    }"
                    :style="`width: ${progress}%`"
                ></div>
            </div>
            
            <div class="px-4 sm:px-5 py-4 flex items-center gap-3 sm:gap-4">
                <div class="relative flex-shrink-0">
                    <div 
                        class="absolute inset-0 rounded-2xl blur-md opacity-50 animate-pulse"
                        :class="{
                            'bg-gradient-to-br from-[#00C853] to-[#00A344]': type === 'success',
                            'bg-gradient-to-br from-[#E40044] to-[#B30034]': type === 'error'
                        }"
                    ></div>
                    <div 
                        class="relative w-10 h-10 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform"
                        :class="{
                            'bg-gradient-to-br from-[#00C853] via-[#00E676] to-[#69F0AE]': type === 'success',
                            'bg-gradient-to-br from-[#E40044] via-[#FF1744] to-[#F50057]': type === 'error'
                        }"
                    >
                        <svg x-show="type === 'success'" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-md">
                            <path d="M20 6L9 17L4 12" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <svg x-show="type === 'error'" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-md">
                            <path d="M18 6L6 18M6 6L18 18" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                
                <div class="flex-1 pr-2 min-w-0">
                    <p 
                        class="font-inter text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider mb-0.5"
                        :class="{
                            'text-[#00C853]': type === 'success',
                            'text-[#E40044]': type === 'error'
                        }"
                        x-text="type === 'success' ? 'Exito' : 'Error'"
                    ></p>
                    <p class="text-gray-900 font-inter text-[13px] sm:text-[15px] font-semibold leading-tight break-words" x-text="message"></p>
                </div>
                
                <div class="flex-shrink-0 relative hidden sm:block">
                    <div 
                        class="absolute inset-0 rounded-xl blur-sm"
                        :class="{
                            'bg-[#00C853]/10': type === 'success',
                            'bg-[#E40044]/10': type === 'error'
                        }"
                    ></div>
                    <div 
                        class="relative w-11 h-11 rounded-xl flex items-center justify-center shadow-md transform hover:scale-110 transition-transform"
                        :class="{
                            'bg-gradient-to-br from-[#00C853] to-[#00A344]': type === 'success',
                            'bg-gradient-to-br from-[#E40044] to-[#B30034]': type === 'error'
                        }"
                    >
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 11V6C9 4.34315 10.3431 3 12 3C13.6569 3 15 4.34315 15 6V10.9673M10.4 21H13.6C15.8402 21 16.9603 21 17.816 20.564C18.5686 20.1805 19.1805 19.5686 19.564 18.816C20 17.9603 20 16.8402 20 14.6V12.2C20 11.0799 20 10.5198 19.782 10.092C19.5903 9.71569 19.2843 9.40973 18.908 9.21799C18.4802 9 17.9201 9 16.8 9H7.2C6.0799 9 5.51984 9 5.09202 9.21799C4.71569 9.40973 4.40973 9.71569 4.21799 10.092C4 10.5198 4 11.0799 4 12.2V14.6C4 16.8402 4 17.9603 4.43597 18.816C4.81947 19.5686 5.43139 20.1805 6.18404 20.564C7.03968 21 8.15979 21 10.4 21Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div 
                class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent to-transparent"
                :class="{
                    'via-[#00C853]/30': type === 'success',
                    'via-[#E40044]/30': type === 'error'
                }"
            ></div>
        </div>
    </div>
    @endif

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</div>
