<div x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0">
    <div class="bg-white pt-[24px] pb-[61px]">
        <nav class="max-w-[1224px] mx-auto text-black font-montserrat text-[12px] leading-[150%] flex items-center gap-1">
            <a wire:navigate href="{{ url('/') }}" class="text-black font-inter text-[12px] font-medium leading-normal">Inicio</a>
            <span class="text-black font-inter text-[12px] font-light leading-normal">›</span>
            <span class="text-black font-inter text-[12px] font-light leading-normal">Carrito</span>
        </nav>
    </div>

    <div class="max-w-[1224px] mx-auto pb-[88px]">
        <div class="overflow-x-auto rounded-t-[4px]">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-black h-[52px] rounded-t-[4px] text-white font-inter text-[16px] font-semibold leading-normal">
                        <th class="text-left"></th>
                        <th class="pl-[22px] text-left">Código</th>
                        <th class="text-left">Descripción</th>
                        <th class="text-right">Precio</th>
                        <th class="text-center pl-[22px]">Descuento</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-center pl-[22px]">Cantidad</th>
                        <th class="text-center">Total</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    <tr>
                        <td colspan="9" class="h-[18px]"></td>
                    </tr>
                    @foreach($items as $item)
    @php
        $precio = $item->precio_unitario;
        $descuento = $item->descuento_unitario;
        $precioFinal = $precio - $descuento;
        $totalItem = $precioFinal * $item->cantidad;
        $porcentajeDescuento = $precio > 0 ? round(($descuento / $precio) * 100) : 0;
    @endphp
                
                        <tr>
                            <td class="rounded-[4px] border border-[#D9D9D9] w-[80px] h-[73px]">
                                <img 
                                    src="{{ $item->producto->image ? asset('storage/'.$item->producto->image) : asset('no-image.png') }}"
                                    class="w-[80px] h-[64px] object-contain"
                                >
                            </td>
                
                            <td class="pl-[22px] text-black font-inter text-[16px] font-normal leading-[25px]">
                                {{ $item->producto->code }}
                            </td>
                
                            <td class="text-black font-inter text-[16px] font-normal leading-[25px] max-w-[130px]">
                                <div>{{ $item->producto->title }}</div>
                            </td>
                
                            <td class="text-[#121212] text-right font-inter text-[16px] font-normal leading-normal">
                                ${{ number_format($precio, 2, ',', '.') }}
                            </td>
                
                            <td class="pl-[22px] text-[#007600] text-center font-inter text-[16px] font-normal leading-normal">
                                ${{ number_format($descuento, 2, ',', '.') }}
                                @if($porcentajeDescuento > 0)
                                <div class="text-[#007600] font-inter text-[14px] font-normal leading-normal">({{ $porcentajeDescuento }}%)</div>
                                @endif
                            </td>
                
                            <td class="text-[#121212] text-right font-inter text-[16px] font-normal leading-normal">
                                ${{ number_format($precioFinal, 2, ',', '.') }}
                            </td>
                
                            <td class="pl-[22px] text-center">
                                <div class="flex justify-center items-center">
                                    <div class="inline-flex text-center justify-center items-center border border-gray-300 rounded-md overflow-hidden h-[36px]">
                                        <input
                                            type="text"
                                            readonly
                                            class="w-[30px] text-center text-sm focus:outline-none bg-white"
                                            value="{{ $item->cantidad }}"
                                        >
                                
                                        <div class="flex flex-col">
                                            <button
                                                type="button"
                                                wire:click="incrementar({{ $item->id }})"
                                                class="w-[24px] h-[18px] flex items-center justify-center hover:bg-gray-100 text-xs rotate-180"
                                            >
                                                <svg class="translate-y-[-3.5px]" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M6 9L12 15L18 9" stroke="#020000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                
                                            <button
                                                type="button"
                                                wire:click="decrementar({{ $item->id }})"
                                                class="w-[24px] h-[18px] flex items-center justify-center hover:bg-gray-100 text-xs"
                                            >
                                                <svg class="-translate-y-[3.5px]" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M6 9L12 15L18 9" stroke="#020000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                
                            <td class="px-4 py-4 text-center font-semibold">
                                ${{ number_format($totalItem, 2, ',', '.') }}
                            </td>
                
                            <td class="px-4 py-4 text-center">
                                <button 
                                    wire:click="eliminar({{ $item->id }})"
                                    wire:confirm="¿Estás seguro de eliminar este producto?"
                                    class="rounded-[4px] border border-[#E40044] w-[48px] h-[39px] text-[#E40044] justify-center flex items-center hover:bg-[#E40044] hover:text-white transition-colors">
                                    <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 18C2.45 18 1.97933 17.8043 1.588 17.413C1.19667 17.0217 1.00067 16.5507 1 16V3H0V1H5V0H11V1H16V3H15V16C15 16.55 14.8043 17.021 14.413 17.413C14.0217 17.805 13.5507 18.0007 13 18H3ZM13 3H3V16H13V3ZM5 14H7V5H5V14ZM9 14H11V5H9V14Z" fill="currentColor"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="9" class="py-[18px]">
                                <img src="{{ asset('linea.png') }}" class="w-full h-[1px]">
                            </td>
                        </tr>
                    
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-[44px]">
            <a 
                wire:navigate 
                href="{{ route('cliente.productos') }}"
                class="inline-flex items-center w-[250px] h-[44px] bg-transparent text-[#E40044] text-center font-inter text-[14px] font-normal leading-normal uppercase rounded-[4px] border border-[#E40044] justify-center">
                AGREGAR MÁS PRODUCTOS
            </a>
        </div>

        <form action="{{ route('cliente.carrito.realizar-pedido') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-[24px] mt-[123px]">
                <div class="space-y-[24px]">
                    <div>
                        <div class="bg-black text-white px-[26px] h-[56px] flex items-center rounded-t-[4px]">
                            <h3 class="font-inter text-[18px] font-semibold">Información importante</h3>
                        </div>
                        <div class="bg-white border border-gray-200 px-[28px] pt-[29px] pb-[31px] rounded-b-[4px]">
                            @if($config && $config->informacion)
                                <div class="prose prose-sm max-w-none">
                                    {!! $config->informacion !!}
                                </div>
                            @else
                                <p class="text-black font-inter text-[16px] font-normal leading-[25px]">- Venta sujeta a disponibilidad en stock</p>
                                <p class="text-black font-inter text-[16px] font-normal leading-[25px]">- Los precios se encuentran expresados en pesos</p>
                                <p class="text-black font-inter text-[16px] font-normal leading-[25px]">- El plazo de entrega se coordina con la empresa</p>
                            @endif
                        </div>
                    </div>
        
                    <div>
                        <div class="bg-black text-white px-[26px] h-[56px] flex items-center rounded-t-[4px]">
                            <h3 class="font-inter text-[18px] font-semibold">Escribinos un mensaje</h3>
                        </div>
                        <div class="bg-white border border-gray-200 px-[27px] pt-[14px] pb-[61px] rounded-b-[4px]">
                            @if($config && $config->escribenos)
                                <div class="prose prose-sm max-w-none">
                                    {!! $config->escribenos !!}
                                </div>
                            @else
                                <p class="text-black font-inter text-[16px] font-normal leading-[25px]">Días especiales de entrega, cambios de domicilio, expresos, requerimientos especiales en la mercadería, exenciones.</p>
                            @endif
                        </div>
                    </div>
        
                    <div>
                        <h3 class="text-[#151515] font-inter text-[22px] font-semibold leading-normal mb-[15px] pl-[24px]">Adjunta un archivo</h3>
                        <div class="flex w-full h-[48px]">
                            <div class="bg-white border border-gray-200 px-[24px] w-full h-[48px] flex items-center rounded-[4px]">
                                <div class="flex items-center gap-4">
                                    <input 
                                        type="file" 
                                        name="archivo"
                                        id="archivo" 
                                        class="hidden"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        onchange="document.getElementById('archivo-nombre').textContent = this.files[0]?.name || 'Seleccionar archivo'"
                                    >
                                    <span class="text-black font-inter text-[16px] font-normal leading-normal" id="archivo-nombre">
                                        Seleccionar archivo
                                    </span>
                                </div>
                            </div>
                            <label 
                                for="archivo" 
                                class="bg-black text-white w-[135px] h-[48px] flex items-center justify-center rounded-[4px] cursor-pointer text-[14px] font-medium uppercase hover:bg-gray-800 transition-colors">
                                ADJUNTAR
                            </label>
                        </div>
                    </div>
                </div>
        
                <div>
                    <div>
                        <div class="bg-black text-white px-[26px] h-[56px] flex items-center rounded-t-[4px]">
                            <h3 class="font-inter text-[18px] font-semibold">Formas de pago</h3>
                        </div>
                        <div class="bg-white border border-gray-200 px-[24px] py-[18px] rounded-b-[4px] space-y-3">
                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center gap-[21px]">
                                    <input 
                                        type="radio" 
                                        name="forma_pago" 
                                        value="contado"
                                        wire:model.live="formaPago"
                                        class="w-[20px] h-[20px] text-[#E4002B] focus:ring-[#E40044]"
                                        required
                                    >
                                    <span class="text-black font-inter text-[16px] font-normal leading-normal">Contado</span>
                                </div>
                                @if($config && $config->contado > 0)
                                <span class="text-[#308C05] text-right font-inter text-[16px] font-normal leading-normal">{{ rtrim(rtrim(number_format($config->contado, 2, '.', ''), '0'), '.') }}% descuento</span>
                                @endif
                            </label>
                    
                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center gap-[21px]">
                                    <input 
                                        type="radio" 
                                        name="forma_pago" 
                                        value="transferencia"
                                        wire:model.live="formaPago"
                                        class="w-[20px] h-[20px] text-[#E4002B] focus:ring-[#E4002B]"
                                    >
                                    <span class="text-black font-inter text-[16px] font-normal leading-normal">Transferencia</span>
                                </div>
                                @if($config && $config->transferencia > 0)
                                <span class="text-[#308C05] text-right font-inter text-[16px] font-normal leading-normal">{{ rtrim(rtrim(number_format($config->transferencia, 2, '.', ''), '0'), '.') }}% descuento</span>
                                @endif
                            </label>
                    
                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center gap-[21px]">
                                    <input 
                                        type="radio" 
                                        name="forma_pago" 
                                        value="cuenta_corriente"
                                        wire:model.live="formaPago"
                                        class="w-[20px] h-[20px] text-[#E4002B] focus:ring-[#E4002B]"
                                    >
                                    <span class="text-black font-inter text-[16px] font-normal leading-normal">Cuenta corriente</span>
                                </div>
                                @if($config && $config->corriente > 0)
                                <span class="text-[#308C05] text-right font-inter text-[16px] font-normal leading-normal">{{ rtrim(rtrim(number_format($config->corriente, 2, '.', ''), '0'), '.') }}% descuento</span>
                                @endif
                            </label>
                        </div>
                    </div>
                    <div class="bg-black text-white px-[26px] h-[56px] flex items-center rounded-t-[4px] mt-[24px]">
                        <h3 class="font-inter text-[18px] font-semibold">Tu pedido</h3>
                    </div>
                    <div class="bg-white border border-gray-200 px-6 pt-[18px] rounded-b-[4px] space-y-4 pb-[12px]">
                        
                        <div class="flex justify-between items-center">
                            <span class="text-black font-inter text-[16px] font-normal leading-normal">Subtotal sin descuento</span>
                            <span class="text-black text-right font-inter text-[16px] font-normal leading-normal">${{ number_format($subtotalSinDescuento, 2, ',', '.') }}</span>
                        </div>
        
                        @if($descuentos > 0 || $descuentoPorPago > 0)
                        @php
                            $totalDescuentos = $descuentos + $descuentoPorPago;
                            $porcentajeTotalDescuento = $subtotalSinDescuento > 0 ? ($totalDescuentos / $subtotalSinDescuento) * 100 : 0;
                        @endphp
                        <div class="flex justify-between items-center text-[#007600]">
                            <span class="text-[#308C05] font-inter text-[16px] font-normal leading-normal">Descuentos ({{ rtrim(rtrim(number_format($porcentajeTotalDescuento, 2, '.', ''), '0'), '.') }}%)</span>
                            <span class="text-[#308C05] font-inter text-[16px] font-normal leading-normal">-${{ number_format($totalDescuentos, 2, ',', '.') }}</span>
                        </div>
                        @endif
        
                        <div class="flex justify-between items-center">
                            <span class="text-black font-inter text-[16px] font-normal leading-normal">Subtotal</span>
                            <span class="text-black text-right font-inter text-[16px] font-normal leading-normal">${{ number_format($subtotal - $descuentoPorPago, 2, ',', '.') }}</span>
                        </div>
        
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-black font-inter text-[16px] font-normal leading-normal">IVA {{ $config ? rtrim(rtrim(number_format($config->iva, 2, '.', ''), '0'), '.') : '21' }}%</span>
                                <span class="text-black text-right font-inter text-[16px] font-normal leading-normal">${{ number_format($iva, 2, ',', '.') }}</span>
                            </div>
                        </div>
        
                        <div>
                            <div class="flex justify-between items-center">
                                <div class="flex gap-2 items-center justify-center text-center">
                                    <span class="text-black font-inter text-[24px] font-semibold leading-normal">Total</span>
                                    <span class="text-black font-inter text-[15px] font-semibold leading-normal pt-1">(IVA incluido)</span>
                                </div>
                                <span class="text-black text-right font-inter text-[24px] font-semibold leading-normal">${{ number_format($total, 2, ',', '.') }}</span>
                            </div>
                        </div>
        
                    </div>
        
                    <div class="pt-4 flex flex-col gap-[25px] lg:flex-row lg:gap-4">
                        <a 
                            href="{{ route('cliente.productos') }}"
                            class="w-full h-[44px] cursor-pointer rounded-[4px] border border-[#E40044] text-[#E40044] text-center font-inter text-[14px] font-normal leading-normal uppercase bg-transparent flex items-center justify-center">
                            CANCELAR
                        </a>
                        <button 
                            type="submit"
                            class="w-full rounded-[4px] cursor-pointer bg-[#E40044] text-white text-center font-inter text-[14px] font-normal leading-normal uppercase h-[44px]">
                            REALIZAR PEDIDO
                        </button>
                    </div>
                </div>
            </div>
        </form>


    </div>

    <!-- Toast Notification - Diseño Moderno -->
    @if(session('success') || session('error'))
    <div 
        x-data="{ 
            show: false, 
            message: '{{ session('success') ?? session('error') }}',
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
        class="fixed left-1/2 transform -translate-x-1/2 z-[100]"
        style="top: 104px;"
    >
        <div class="relative bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.15)] border border-white/50 backdrop-blur-xl overflow-hidden min-w-[380px] max-w-[480px]">
            <!-- Barra de progreso superior -->
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
            
            <div class="px-5 py-4 flex items-center gap-4">
                <!-- Icono animado con efecto glow -->
                <div class="relative flex-shrink-0">
                    <div 
                        class="absolute inset-0 rounded-2xl blur-md opacity-50 animate-pulse"
                        :class="{
                            'bg-gradient-to-br from-[#00C853] to-[#00A344]': type === 'success',
                            'bg-gradient-to-br from-[#E40044] to-[#B30034]': type === 'error'
                        }"
                    ></div>
                    <div 
                        class="relative w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform"
                        :class="{
                            'bg-gradient-to-br from-[#00C853] via-[#00E676] to-[#69F0AE]': type === 'success',
                            'bg-gradient-to-br from-[#E40044] via-[#FF1744] to-[#F50057]': type === 'error'
                        }"
                    >
                        <!-- Icono de check para success -->
                        <svg x-show="type === 'success'" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-md">
                            <path d="M20 6L9 17L4 12" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <!-- Icono de X para error -->
                        <svg x-show="type === 'error'" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-md">
                            <path d="M18 6L6 18M6 6L18 18" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Contenido del mensaje -->
                <div class="flex-1 pr-2">
                    <p 
                        class="font-inter text-[11px] font-semibold uppercase tracking-wider mb-0.5"
                        :class="{
                            'text-[#00C853]': type === 'success',
                            'text-[#E40044]': type === 'error'
                        }"
                        x-text="type === 'success' ? 'Éxito' : 'Error'"
                    ></p>
                    <p class="text-gray-900 font-inter text-[15px] font-semibold leading-tight" x-text="message"></p>
                </div>
                
                <!-- Icono del carrito con animación -->
                <div class="flex-shrink-0 relative">
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
            
            <!-- Efecto de brillo inferior -->
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
</div>