<div x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0">
    <div class="bg-white pt-[24px] pb-[61px]">
        <nav class="max-w-[1224px] mx-auto text-black font-montserrat text-[12px] leading-[150%] flex items-center gap-1">
            <a wire:navigate href="{{ url('/') }}" class="text-black font-inter text-[12px] font-medium leading-normal drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Inicio</a>
            <span class="text-black font-inter text-[12px] font-light leading-normal drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">›</span>
            <span class="text-black font-inter text-[12px] font-light leading-normal drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Productos</span>
        </nav>
    </div>
    
    <div class="pt-[26px] pb-[20px] bg-[#F4F4F4]">
        <div class="max-w-[1224px] mx-auto">
            <div class="mb-[26px] flex gap-[28px]">
                <input
                    type="text"
                    wire:model.defer="search"
                    placeholder="Marca / Equivalencias / Atributo / Código"
                    class="w-[912px] h-[44px] rounded-[4px] border border-[#F1F1F1] bg-white placeholder:text-black placeholder:font-inter placeholder:text-[15px] placeholder:font-normal placeholder:leading-normal px-[16px]"
                />
                <div class="flex gap-[36px]">
                    <label class="flex items-center gap-[8px] cursor-pointer" wire:click="toggleTipo('nuevo')">
                        <img src="{{ asset($tipo === 'nuevo' ? 'check1.svg' : 'check0.svg') }}" 
                             alt="check" 
                             class="w-5 h-5">
                        <span class="text-black font-inter text-[15px] font-normal leading-normal">Nuevo</span>
                    </label>
                
                    <label class="flex items-center gap-[8px] cursor-pointer" wire:click="toggleTipo('recambio')">
                        <img src="{{ asset($tipo === 'recambio' ? 'check1.svg' : 'check0.svg') }}" 
                             alt="check" 
                             class="w-5 h-5">
                        <span class="text-black font-inter text-[15px] font-normal leading-normal">Recambio</span>
                    </label>
                </div>
            </div>
        
            <div class="grid grid-cols-1 md:grid-cols-4 gap-[24px]">
                <div class="space-y-[16px]">
                    <div class="flex items-center gap-[10px] text-black font-inter text-[20px] font-bold leading-[140%]">
                        <img src="/cate.svg" class="w-[29px] h-[29px]">
                        Categoría
                    </div>
                
                    <div class="relative">
                        <select wire:model.live="categoriaId" 
                                class="cursor-pointer w-full h-[44px] px-[12px] bg-white border border-[#E5E5E5] rounded-[4px] appearance-none pr-10"
                                id="categoriaSelect">
                            <option class="text-black font-inter text-[15px] font-normal leading-normal" value="">Categorías</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                            @endforeach
                        </select>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none transition-transform duration-200" id="arrowCategoria">
                            <svg width="13" height="8" viewBox="0 0 13 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.06226 7.5L7.84993e-05 1.88257e-07L12.1244 -8.71687e-07L6.06226 7.5Z" fill="#747474"/>
                            </svg>
                        </div>
                    </div>
                
                    @if($categoriaId && $subcategoriasActuales->count() > 0)
                        @foreach($subcategoriasActuales as $sub)
                            <div>
                                <input
                                    type="text"
                                    wire:model.defer="filtrosSubcategorias.{{ $sub->id }}"
                                    placeholder="{{ $sub->title }}"
                                    class="w-full h-[44px] px-[12px] rounded-[4px] border border-[#F1F1F1] bg-white placeholder:text-[#636363] placeholder:font-inter placeholder:text-[15px] placeholder:font-normal placeholder:leading-normal"
                                />
                            </div>
                        @endforeach
                    @endif
                </div>
                
                <div class="space-y-[16px]">
                    <div class="flex items-center gap-[10px] text-black font-inter text-[20px] font-bold leading-[140%]">
                        <img src="/marc.svg" class="w-[39px] h-[28px]">
                        Marca
                    </div>
                
                    <div class="relative">
                        <select wire:model.live="marcaId" 
                                class="cursor-pointer w-full h-[44px] px-[12px] rounded-[4px] border border-[#F1F1F1] bg-white appearance-none pr-10"
                                id="marcaSelect">
                            <option value="" class="text-black font-inter text-[15px] font-normal leading-normal">Marca</option>
                            @foreach($marcas as $marca)
                                <option value="{{ $marca->id }}">{{ $marca->title }}</option>
                            @endforeach
                        </select>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none transition-transform duration-200" id="arrowMarca">
                            <svg width="13" height="8" viewBox="0 0 13 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.06226 7.5L7.84993e-05 1.88257e-07L12.1244 -8.71687e-07L6.06226 7.5Z" fill="#747474"/>
                            </svg>
                        </div>
                    </div>
                
                    @if($marcaId && $marcas->firstWhere('id', $marcaId)?->modelos->count() > 0)
                        <div class="relative">
                            <select wire:model.defer="modeloId" 
                                    class="cursor-pointer w-full h-[44px] px-[12px] rounded-[4px] border border-[#F1F1F1] bg-white appearance-none pr-10"
                                    id="modeloSelect">
                                <option class="text-black font-inter text-[15px] font-normal leading-normal" value="">Modelo</option>
                                @foreach($marcas->firstWhere('id', $marcaId)->modelos as $modelo)
                                    <option value="{{ $modelo->id }}">{{ $modelo->title }}</option>
                                @endforeach
                            </select>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none transition-transform duration-200" id="arrowModelo">
                                <svg width="13" height="8" viewBox="0 0 13 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.06226 7.5L7.84993e-05 1.88257e-07L12.1244 -8.71687e-07L6.06226 7.5Z" fill="#747474"/>
                                </svg>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="space-y-[11px]">
                    <div class="flex items-center gap-[10px] text-black font-inter text-[20px] font-bold leading-[140%]">
                        <img src="code.svg" class="w-[34px] h-[34px]">
                        Código
                    </div>
                
                    <div class="relative">
                        <input type="text" 
                               wire:model.defer="codigo" 
                               placeholder="Código Estatomac"
                               class="w-full h-[44px] px-[12px] rounded-[4px] border border-[#F1F1F1] bg-white text-black font-inter text-[15px] font-normal leading-normal placeholder:text-[#636363]">
                    </div>
                </div>
                
                <div class="space-y-[16px] relative">
                    <div class="flex items-center gap-[10px] text-black font-inter text-[20px] font-bold leading-[140%]">
                        <img src="equi.svg" class="w-[29px] h-[29px]">
                        Equivalencias
                    </div>
                
                    <div class="relative">
                        <input type="text" 
                               wire:model.defer="equivalencia" 
                               placeholder="Equivalencia"
                               class="w-full h-[44px] px-[12px] rounded-[4px] border border-[#F1F1F1] bg-white text-black font-inter text-[15px] font-normal leading-normal placeholder:text-[#636363]">
                    </div>
                    
                    <span class="absolute top-24 left-0 text-[#777] font-inter text-[15px] font-normal leading-normal">
                        (GV, Dipra, PH, Ferman, Unifap)
                    </span>
                </div>
            </div>

            <div class="flex justify-center gap-[18px] mt-[40px]">
                <button
                    wire:click="buscar"
                    class="bg-[#E4002B] cursor-pointer text-white px-[32px] h-[44px] rounded-[4px] text-[14px] font-inter uppercase hover:bg-[#b8001f] transition-colors"
                >
                    Buscar
                </button>
            
                <button
                    wire:click="limpiarFiltros"
                    class="border border-[#E4002B] cursor-pointer text-[#E4002B] px-[32px] h-[44px] rounded-[4px] text-[14px] font-inter uppercase hover:bg-[#E4002B] hover:text-white transition-colors"
                >
                    Limpiar
                </button>
            </div>
        </div>
    </div>
    
    <div class="max-w-[1224px] mx-auto pt-[43px] pb-[88px]">
        @if($productos && $productos->count())
            <div class="overflow-x-auto rounded-t-[4px]">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-black h-[52px] rounded-t-[4px] text-[14px] text-white font-inter text-[16px] font-semibold leading-normal">
                            <th class="text-left"></th>
                            <th class="pl-[22px] text-left">Código</th>
                            <th class="text-left">Descripción</th>
                            <th class="text-right">Precio</th>
                            <th class="text-center pl-[22px]">Descuento</th>
                            <th class="text-right w-[40px]">Precio con desc</th>
                            <th class="text-center pl-[22px]">Cantidad</th>
                            <th class="text-center">Total</th>
                            <th></th>
                        </tr>
                    </thead>
            
                    <tbody class="bg-white">
                        <tr>
                            <td colspan="9" class="h-[18px]"></td>
                        </tr>
                        @foreach($productos as $producto)
                            @php
                                $precio = $producto->precio;
                                $descuento = $producto->descuento ?? 0;
                            
                                if ($descuento <= 1) {
                                    $porcentajeDescuento = $descuento * 100;
                                    $montoDescuento = $precio * $descuento;
                                } elseif ($descuento <= 100) {
                                    $porcentajeDescuento = $descuento;
                                    $montoDescuento = $precio * ($descuento / 100);
                                } else {
                                    $montoDescuento = $descuento;
                                    $porcentajeDescuento = ($precio > 0) ? ($descuento / $precio) * 100 : 0;
                                }
                            
                                $precioFinal = $precio - $montoDescuento;
                            @endphp
                    
                            <tr>
                                <td class="rounded-[4px] border border-[#D9D9D9] w-[80px] h-[73px]">
                                    <img 
                                        src="{{ $producto->image ? asset('storage/'.$producto->image) : asset('no-image.png') }}"
                                        class="w-[80px] h-[64px] object-contain"
                                    >
                                </td>
                    
                                <td class="pl-[22px] text-black font-inter text-[16px] font-normal leading-[25px]">
                                    {{ $producto->code }}
                                </td>
                    
                                <td class="text-black font-inter text-[16px] font-normal leading-[25px] max-w-[130px]">
                                    <div>{{ $producto->title }}</div>
                                </td>
                    
                                <td class="text-[#121212] text-right font-inter text-[16px] font-normal leading-normal">
                                    ${{ number_format($precio, 2, ',', '.') }}
                                </td>
                    
                                <td class="pl-[22px] text-[#007600] text-center font-inter text-[16px] font-normal leading-normal">
                                    ${{ number_format($montoDescuento, 2, ',', '.') }}
                                
                                    @if($porcentajeDescuento > 0)
                                        <div class="text-[#007600] font-inter text-[14px] font-normal leading-normal">
                                            ({{ round($porcentajeDescuento, 2) }}%)
                                        </div>
                                    @endif
                                </td>
                    
                                <td class="text-[#121212] text-right font-inter text-[16px] font-normal leading-normal">
                                    ${{ number_format($precioFinal, 2, ',', '.') }}
                                </td>
                    
                                <td class="pl-[22px] text-center flex justify-center items-center pt-[18px]">
                                    <div class="inline-flex text-center justify-center items-center border border-gray-300 rounded-md overflow-hidden h-[36px]">
                                        <input
                                            type="text"
                                            readonly
                                            class="w-[30px] text-center text-sm focus:outline-none bg-white"
                                            wire:model="cantidades.{{ $producto->id }}"
                                        >
                                
                                        <div class="flex flex-col">
                                            <button
                                                type="button"
                                                wire:click="incrementar({{ $producto->id }})"
                                                class="w-[24px] h-[18px] flex items-center justify-center hover:bg-gray-100 text-xs rotate-180"
                                            >
                                                <svg class="translate-y-[-3.5px]" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <g opacity="1">
                                                    <path d="M6 9L12 15L18 9" stroke="#020000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </g>
                                                </svg>
                                            </button>
                                
                                            <button
                                                type="button"
                                                wire:click="decrementar({{ $producto->id }})"
                                                class="w-[24px] h-[18px] flex items-center justify-center hover:bg-gray-100 text-xs"
                                            >
                                                <svg class="-translate-y-[3.5px]" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <g opacity="1">
                                                    <path d="M6 9L12 15L18 9" stroke="#020000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </g>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-4 py-4 text-center font-semibold">
                                    ${{ number_format($precioFinal * ($cantidades[$producto->id] ?? 1), 2, ',', '.') }}
                                </td>
                    
                                <td class="px-4 py-4 text-center">
                                    <button 
                                        wire:click="agregarAlCarrito({{ $producto->id }})"
                                        class="rounded-[4px] cursor-pointer border border-[#E40044] w-[48px] h-[39px] text-[#E40044] justify-center flex items-center hover:bg-[#E40044] hover:text-white transition-colors">
                                        <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.50416 16C4.09128 16 3.73795 15.8435 3.44418 15.5304C3.15041 15.2173 3.00327 14.8405 3.00277 14.4C3.00277 13.96 3.14991 13.5835 3.44418 13.2704C3.73845 12.9573 4.09178 12.8005 4.50416 12.8C4.91704 12.8 5.27062 12.9568 5.56489 13.2704C5.85916 13.584 6.00605 13.9605 6.00555 14.4C6.00555 14.84 5.85866 15.2168 5.56489 15.5304C5.27112 15.844 4.91754 16.0005 4.50416 16ZM12.0111 16C11.5982 16 11.2449 15.8435 10.9511 15.5304C10.6573 15.2173 10.5102 14.8405 10.5097 14.4C10.5097 13.96 10.6568 13.5835 10.9511 13.2704C11.2454 12.9573 11.5987 12.8005 12.0111 12.8C12.424 12.8 12.7776 12.9568 13.0718 13.2704C13.3661 13.584 13.513 13.9605 13.5125 14.4C13.5125 14.84 13.3656 15.2168 13.0718 15.5304C12.7781 15.844 12.4245 16.0005 12.0111 16ZM3.86607 3.2L5.66774 7.2H10.9226L12.987 3.2H3.86607ZM3.15291 1.6H14.2256C14.5134 1.6 14.7324 1.7368 14.8825 2.0104C15.0326 2.284 15.0389 2.56053 14.9013 2.84L12.2363 7.96C12.0987 8.22667 11.9143 8.43333 11.683 8.58C11.4518 8.72667 11.1983 8.8 10.9226 8.8H5.32992L4.50416 10.4H13.5125V12H4.50416C3.94114 12 3.51575 11.7368 3.22798 11.2104C2.94022 10.684 2.9277 10.1605 3.19045 9.64L4.20388 7.68L1.50139 1.6H0V0H2.43975L3.15291 1.6Z" fill="currentColor"/>
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

            <!-- Paginación -->
            <div class="mt-[48px]">
                <!-- Info y selector de items por página -->
                <div class="flex items-center justify-between mb-[24px]">
                    <div class="text-[#666] font-inter text-[14px]">
                        Mostrando 
                        <span class="font-semibold text-black">{{ $productos->firstItem() }}</span> 
                        a 
                        <span class="font-semibold text-black">{{ $productos->lastItem() }}</span> 
                        de 
                        <span class="font-semibold text-black">{{ $productos->total() }}</span> 
                        productos
                    </div>

                    <div class="flex items-center gap-[12px]">
                        <span class="text-[#666] font-inter text-[14px]">Mostrar:</span>
                        <select wire:model.live="perPage" class="h-[36px] px-[12px] rounded-[4px] border border-[#E5E5E5] bg-white font-inter text-[14px] cursor-pointer">
                            <option value="12">12</option>
                            <option value="24">24</option>
                            <option value="48">48</option>
                            <option value="96">96</option>
                        </select>
                    </div>
                </div>

                <!-- Navegación de páginas -->
                @if ($productos->hasPages())
                    <div class="flex items-center justify-center gap-[8px]">
                        {{-- Botón Primera Página --}}
                        @if ($productos->onFirstPage())
                            <button disabled class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 12L4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        @else
                            <button wire:click="gotoPage(1)" class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#E40044] hover:text-white hover:border-[#E40044] transition-colors">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 12L4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        @endif

                        {{-- Botón Anterior --}}
                        @if ($productos->onFirstPage())
                            <button disabled class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        @else
                            <button wire:click="previousPage" class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#E40044] hover:text-white hover:border-[#E40044] transition-colors">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        @endif

                        {{-- Números de Página --}}
                        @php
                            $currentPage = $productos->currentPage();
                            $lastPage = $productos->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp

                        {{-- Primera página si no está en el rango --}}
                        @if ($startPage > 1)
                            <button wire:click="gotoPage(1)" class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#E40044] hover:text-white hover:border-[#E40044] transition-colors font-inter text-[14px]">
                                1
                            </button>
                            @if ($startPage > 2)
                                <span class="text-[#666] font-inter text-[14px]">...</span>
                            @endif
                        @endif

                        {{-- Páginas en el rango --}}
                        @for ($page = $startPage; $page <= $endPage; $page++)
                            @if ($page == $currentPage)
                                <button class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] bg-[#E40044] text-white font-inter text-[14px] font-semibold">
                                    {{ $page }}
                                </button>
                            @else
                                <button wire:click="gotoPage({{ $page }})" class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#E40044] hover:text-white hover:border-[#E40044] transition-colors font-inter text-[14px]">
                                    {{ $page }}
                                </button>
                            @endif
                        @endfor

                        {{-- Última página si no está en el rango --}}
                        @if ($endPage < $lastPage)
                            @if ($endPage < $lastPage - 1)
                                <span class="text-[#666] font-inter text-[14px]">...</span>
                            @endif
                            <button wire:click="gotoPage({{ $lastPage }})" class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#E40044] hover:text-white hover:border-[#E40044] transition-colors font-inter text-[14px]">
                                {{ $lastPage }}
                            </button>
                        @endif

                        {{-- Botón Siguiente --}}
                        @if ($productos->hasMorePages())
                            <button wire:click="nextPage" class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#E40044] hover:text-white hover:border-[#E40044] transition-colors">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        @else
                            <button disabled class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        @endif

                        {{-- Botón Última Página --}}
                        @if ($productos->hasMorePages())
                            <button wire:click="gotoPage({{ $productos->lastPage() }})" class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#E40044] hover:text-white hover:border-[#E40044] transition-colors">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        @else
                            <button disabled class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        @else
            <div class="text-center text-[#777] py-20">
                No se encontraron productos
            </div>
        @endif
    </div>

@if(count($productosEnOferta) > 0)
@php
    $producto = $productosEnOferta[$productoActualModal];
    $precio = $producto['precio'];
    $descuento = $producto['descuento'] ?? 0;
    
    if ($descuento <= 1) {
        $porcentajeDescuento = $descuento * 100;
    } elseif ($descuento <= 100) {
        $porcentajeDescuento = $descuento;
    } else {
        $porcentajeDescuento = ($precio > 0) ? ($descuento / $precio) * 100 : 0;
    }
@endphp

<div 
    x-data="{ 
        show: false,
        intervalo: null,
        iniciarCarrusel() {
            this.intervalo = setInterval(() => {
                @this.call('siguienteProductoModal');
            }, 4000);
        },
        detenerCarrusel() {
            if (this.intervalo) {
                clearInterval(this.intervalo);
            }
        }
    }"
    x-init="
        setTimeout(() => {
            show = true;
            iniciarCarrusel();
        }, 2000);
    "
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <div 
        class="fixed inset-0 bg-gradient-to-b from-transparent to-white to-20%"
        x-show="show"
        x-transition:enter="transition-opacity ease-out duration-500"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        @click="detenerCarrusel(); show = false; setTimeout(() => $wire.cerrarModalOfertas(), 300)"
        style="background: rgba(0, 0, 0, 0.4);"
    ></div>
    
    <div class="flex items-center justify-center min-h-screen px-4">
        <div 
            x-show="show"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 scale-90 -translate-y-10"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-10"
            class="relative bg-white rounded-[4px] max-w-[780px] w-full h-[480px] shadow-2xl overflow-hidden cursor-pointer"
            @click.stop="@this.call('siguienteProductoModal')"
        >
            <button 
                @click.stop="detenerCarrusel(); show = false; setTimeout(() => $wire.cerrarModalOfertas(), 300)"
                class="absolute top-2 right-4 z-10 w-10 h-10 flex items-center justify-center bg-white cursor-pointer rounded-full transition-colors"
            >
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2 h-full">
                <div class="relative bg-[linear-gradient(90deg,#E4004480_0.22%,#FFF_80.76%)]">
                    <img src="/oferta.png" alt="Patrón Hexagonal" class="h-[485px] object-contain">
                </div>

                <div class="pt-[52px] md:p-12 flex flex-col justify-center relative">
                    <div 
                        wire:key="producto-{{ $productoActualModal }}"
                        x-data="{ visible: false }"
                        x-init="setTimeout(() => { visible = true }, 50)"
                        x-show="visible"
                        x-transition:enter="transition ease-out duration-400"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="w-full"
                    >
                        <div class="text-center">
                            <h2 class="text-[#E40044] text-center font-inter text-[24px] font-semibold leading-[25px]">OFERTA</h2>
                            <p class="text-black text-center font-inter text-[12px] font-normal leading-[25px]">HASTA 31/12</p>
                        </div>

                        <div class="text-center mb-6">
                            <h3 class="text-black text-center font-inter text-[40px] font-semibold leading-[25px] pt-[11px]">
                                {{ $producto['code'] ?? 'N/A' }}
                            </h3>
                        </div>

                        <div class="flex justify-center pt-[46px]">
                            <img 
                                src="{{ $producto['image'] ? asset('storage/' . $producto['image']) : asset('no-image.png') }}" 
                                alt="{{ $producto['title'] }}"
                                class="max-h-[140px] object-contain"
                            >
                        </div>

                        <div class="text-center mb-8">
                            <p class="text-[#E40044] text-center font-inter text-[16px] font-extrabold leading-[25px]">
                                {{ round($porcentajeDescuento) }}% de descuento
                            </p>
                        </div>

                        <button
                            @click.stop="$wire.agregarAlCarritoDesdeModal({{ $producto['id'] }})"
                            class="w-[164px] h-[44px] rounded-[4px] bg-[#E40044] text-white text-center font-inter text-[14px] font-normal leading-normal uppercase mx-auto flex items-center justify-center hover:bg-[#B30034] transition-colors"
                        >
                            AGREGAR A CARRITO
                        </button>

                        @if(count($productosEnOferta) > 1)
                        <div class="flex justify-center items-center gap-4 mt-6">
                            <div class="flex gap-2">
                                @foreach($productosEnOferta as $index => $prod)
                                <div 
                                    @click.stop="$wire.set('productoActualModal', {{ $index }})"
                                    class="w-2 h-2 rounded-full cursor-pointer transition-all {{ $index === $productoActualModal ? 'bg-[#E40044] scale-125' : 'bg-gray-300 hover:bg-gray-400' }}"
                                ></div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div 
    x-data="{ 
        show: false, 
        message: '',
        type: 'success',
        progress: 100,
        progressInterval: null,
        init() {
            @if(session('toast'))
                this.lanzarToast('{{ session('toast.message') }}', '{{ session('toast.type') }}');
            @endif
            
            Livewire.on('producto-agregado', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;
                this.lanzarToast(payload.message || 'Operación exitosa', payload.type || 'success');
            });
        },
        lanzarToast(msg, tipo) {
            this.message = msg;
            this.type = tipo;
            this.show = true;
            this.animarProgreso();
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
    x-cloak
    x-show="show"
    x-transition:enter="transition ease-out duration-400"
    x-transition:enter-start="opacity-0 scale-90 -translate-y-8"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
    class="fixed left-1/2 transform -translate-x-1/2 z-[100]"
    style="top: 104px; display: none;"
>
    <div class="relative bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.15)] border border-white/50 backdrop-blur-xl overflow-hidden min-w-[380px] max-w-[480px]">
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
                    <svg x-show="type === 'success'" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-md">
                        <path d="M20 6L9 17L4 12" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <svg x-show="type === 'error'" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-md">
                        <path d="M18 6L6 18M6 6L18 18" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            
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
        
        <div 
            class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent to-transparent"
            :class="{
                'via-[#00C853]/30': type === 'success',
                'via-[#E40044]/30': type === 'error'
            }"
        ></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoriaSelect = document.getElementById('categoriaSelect');
    const arrowCategoria = document.getElementById('arrowCategoria');
    const marcaSelect = document.getElementById('marcaSelect');
    const arrowMarca = document.getElementById('arrowMarca');

    if (categoriaSelect && arrowCategoria) {
        categoriaSelect.addEventListener('click', function() {
            const isRotated = arrowCategoria.style.transform.includes('rotate(180deg)');
            arrowCategoria.style.transform = isRotated ? 'rotate(0deg)' : 'rotate(180deg)';
        });
    }
    
    if (marcaSelect && arrowMarca) {
        marcaSelect.addEventListener('click', function() {
            const isRotated = arrowMarca.style.transform.includes('rotate(180deg)');
            arrowMarca.style.transform = isRotated ? 'rotate(0deg)' : 'rotate(180deg)';
        });
    }
    
    document.addEventListener('livewire:navigated', function() {
        const modeloSelect = document.getElementById('modeloSelect');
        const arrowModelo = document.getElementById('arrowModelo');
        
        if (modeloSelect && arrowModelo) {
            modeloSelect.addEventListener('click', function() {
                const isRotated = arrowModelo.style.transform.includes('rotate(180deg)');
                arrowModelo.style.transform = isRotated ? 'rotate(0deg)' : 'rotate(180deg)';
            });
        }
    });
});
</script>
</div>