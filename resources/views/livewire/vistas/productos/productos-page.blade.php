<div x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0">
    @if($banner && $banner->image_banner)
        <section class="relative w-full h-[380px] max-[767px]:h-[300px] max-[639px]:h-[250px]">
            <img src="{{ asset('storage/' . $banner->image_banner) }}"
                 alt="{{ $banner->title }}"
                 class="w-full h-full object-cover grayscale-[100%] contrast-155">

            <div class="absolute top-[114px] left-0 right-0 z-30 max-[1199px]:top-[80px]">
                <div class="max-w-[1224px] mx-auto">
                    <nav class="text-white font-montserrat text-[12px] leading-[150%] flex items-center gap-1">
                        <a wire:navigate href="{{ url('/') }}" class="font-bold drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Inicio</a>
                        <span class="drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">›</span>
                        <span class="drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Productos</span>
                    </nav>
                </div>
            </div>

            <div class="absolute inset-0 bg-[rgba(170,65,65,0.5)]"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-black/20 to-black/20"></div>

            <div class="absolute inset-0 flex top-[245px] max-w-[1224px] mx-auto">
                <h1 class="text-white font-inter text-[40px] font-bold max-[767px]:text-[36px] max-[639px]:text-[28px] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">
                    {{ $banner->title }}
                </h1>
            </div>
        </section>
    @endif
    
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
                        <img src="cate.svg" class="w-[29px] h-[29px]">
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
                        <img src="marc.svg" class="w-[39px] h-[28px]">
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
                    class="bg-[#E4002B] text-white px-[32px] h-[44px] rounded-[4px] text-[14px] font-inter uppercase cursor-pointer hover:bg-[#b8001f] transition-colors"
                >
                    Buscar
                </button>
            
                <button
                    wire:click="limpiarFiltros"
                    class="border border-[#E4002B] text-[#E4002B] px-[32px] h-[44px] rounded-[4px] text-[14px] font-inter uppercase cursor-pointer hover:bg-[#E4002B] hover:text-white transition-colors"
                >
                    Limpiar
                </button>
            </div>
        </div>
    </div>
    
    <div class="max-w-[1224px] mx-auto pt-[55px] pb-[88px]">
        @if(!$hayFiltros)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[24px]">
        @foreach($categorias as $categoria)
            <button wire:click="seleccionarCategoria({{ $categoria->id }})"
               class="cursor-pointer group relative bg-white rounded-[4px] overflow-hidden transition-all duration-300 text-left">
               
                @if($categoria->image)
                    <div class="w-[392px] h-[196px] overflow-hidden">
                        <img src="{{ asset('storage/' . $categoria->image) }}"
                             alt="{{ $categoria->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                @endif

                <div>
                    <h3 class="text-[#111010] font-inter text-[16px] font-normal leading-[28px] mt-[11px]">
                        {{ $categoria->title }}
                    </h3>
                </div>
            </button>
        @endforeach
    </div>
@else
    @if($productos && $productos->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-[24px]">
            @foreach($productos as $producto)
                <a href="{{ route('productos.detalle', $producto->id) }}" class="group relative bg-white overflow-hidden rounded-[4px] h-[312px] block">
                    
                    <span class="absolute top-0 left-0 {{ $producto->nuevo === 'nuevo' ? 'bg-[#E40044]' : 'bg-[#111]' }} text-white text-[12px] font-semibold uppercase w-[107px] h-[29px] text-center font-inter text-[14px] font-bold leading-[30px] z-20">
                        {{ strtoupper($producto->nuevo) }}
                    </span>
                    
                    <div class="relative w-[288px] h-[232px] flex items-center justify-center overflow-hidden rounded-[4px] border border-[#D9D9D9]">
                        <div class="absolute inset-0 bg-[#FFF5E6] opacity-0 group-hover:opacity-30 transition-opacity duration-300 z-10"></div>
                        <img
                            src="{{ asset('storage/' . $producto->image) }}"
                            alt="{{ $producto->title }}"
                            class="h-full w-full object-contain"
                        >
                    </div>
            
                    <div class="pt-[12px] px-[13px]">
                        <p class="text-[#E40044] font-inter text-[14px] font-bold uppercase leading-[25px] pb-[2px]">
                            {{ $producto->code ?? '' }}
                        </p>
            
                        <h3 class="text-black font-inter text-[16px] font-normal uppercase leading-[22px] line-clamp-2">
                            {{ $producto->title }}
                        </h3>
                    </div>
                </a>
            @endforeach
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
        <p class="text-center text-[14px] text-[#777]">
            No se encontraron productos con los filtros seleccionados
        </p>
    @endif
@endif
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