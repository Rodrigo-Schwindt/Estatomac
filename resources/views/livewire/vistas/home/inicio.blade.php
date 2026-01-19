<div 
    x-data="{ show: false }" 
    x-init="setTimeout(() => show = true, 50)" 
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 transform -translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0"
>
    @if($sliders->count() > 0)
    <div 
        x-data="{
            currentSlideIndex: 1,
            autoSlide: null,
            init() { this.startAutoSlide() },
            stopAutoSlide() { if (this.autoSlide) clearInterval(this.autoSlide) },
            previous() {
                this.stopAutoSlide()
                this.currentSlideIndex = this.currentSlideIndex > 1 ? this.currentSlideIndex - 1 : {{ $sliders->count() }}
                this.startAutoSlide()
            },
            next() {
                this.stopAutoSlide()
                this.currentSlideIndex = this.currentSlideIndex < {{ $sliders->count() }} ? this.currentSlideIndex + 1 : 1
                this.startAutoSlide()
            },
            handleMouseDown(e) { this.mouseStartX = e.clientX; this.isDragging = true },
            handleMouseMove(e) { if (this.isDragging) this.mouseEndX = e.clientX },
            handleMouseUp() {
                if (!this.isDragging) return
                this.isDragging = false
                const diff = this.mouseStartX - this.mouseEndX
                if (Math.abs(diff) > 50) diff > 0 ? this.next() : this.previous()
            }
        }"
        x-on:mousedown="handleMouseDown"
        x-on:mousemove="handleMouseMove"
        x-on:mouseup="handleMouseUp"
        x-on:mouseleave="isDragging = false"
        class="relative w-full overflow-hidden select-none"
    >
        <div class="relative h-[672px] max-lg:h-[520px] max-md:h-[420px] max-sm:h-[520px] w-full">

            @foreach($sliders as $index => $slider)
            <template x-if="currentSlideIndex == {{ $index + 1 }}">
                <div
                    x-cloak
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0"
                >
                    <div class="
                        absolute inset-0 z-10 flex flex-col 
                        pt-[230px]
                        mb-[25px] 
                        max-w-[1224px] mx-auto 
                        max-xl:px-6 
                        max-lg:pt-[200px] 
                        max-md:pt-[120px] 
                        max-sm:pt-[160px]
                        max-md:text-center
                        max-md:items-center
                    ">
                        <h1 class="
                            w-[720px] text-white font-inter text-[46px] font-semibold leading-tight
                        ">
                            {{ $slider->title }}
                        </h1>
                        <p
                            x-data="{
                                expandIfLong() {
                                    const lineHeight = parseFloat(getComputedStyle(this.$refs.desc).lineHeight);
                                    const lines = Math.round(this.$refs.desc.scrollHeight / lineHeight);
                                    if (lines > 3) {
                                        this.$refs.desc.style.maxWidth = '820px';
                                    }
                                }
                            }"
                            x-init="expandIfLong()"
                            x-ref="desc"
                            class="
                                w-[620px] text-white font-inter text-[18px] font-light leading-normal
                            "
                        >
                            {{ preg_replace('/(&nbsp;)+$/', '', strip_tags($slider->description)) }}
                        </p>
                        <a 
                        wire:navigate href="/productos"
                        class="
                        absolute 
                        bottom-[66px] 
                        flex w-[184px] px-[5px] py-[10px] justify-center items-center 
                        w-[164px] h-[44px] 
                        text-white text-center font-inter text-[14px] font-normal leading-normal uppercase 
                        bg-transparent 
                        rounded-[4px] border border-white 
                        transition-colors duration-300
                        hover:border-[#E4002B] hover:text-[#E4002B]
                        "
                        >
                            Ver productos
                        </a>

                        @if($sliders->count() > 1)
                        <div class="
                            absolute 
                            bottom-[24px] 
                            z-20 flex gap-3 
                        
                            max-xl:bottom-[30px]
                            max-md:static
                            max-md:mt-[20px]
                            max-md:justify-center
                        ">
                            @foreach($sliders as $i => $s)
                            <button
                                class="transition w-[44px] h-[6px] max-md:w-[28px] max-md:h-[5px] cursor-pointer"
                                x-on:click="currentSlideIndex = {{ $i + 1 }}"
                                :class="currentSlideIndex === {{ $i + 1 }} ? 'bg-white drop-shadow-lg' : 'bg-white/50'"
                            ></button>
                            @endforeach
                        </div>
                        @endif
                        
                    </div>

                    @php
                        $ext = strtolower(pathinfo($slider->image, PATHINFO_EXTENSION));
                        $isVideo = in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'avi']);
                    @endphp

                    <div class="absolute inset-0">
                        @if($isVideo)
                            <video class="absolute w-full h-full inset-0 object-cover" autoplay loop muted playsinline>
                                <source src="{{ Storage::url($slider->image) }}" type="video/{{ $ext }}">
                            </video>
                        @else
                            <img 
                                class="absolute w-full h-full inset-0 object-cover"
                                src="{{ Storage::url($slider->image) }}"
                                alt="{{ $slider->title }}"
                            />
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/40 to-black/40"></div>

                    </div>
                </div>
            </template>
            @endforeach

        </div>
    </div>
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
                                class="w-full h-[44px] px-[12px] bg-white border border-[#E5E5E5] rounded-[4px] appearance-none pr-10 cursor-pointer"
                                id="categoriaSelect">
                            <option class="text-black font-inter text-[15px] font-normal leading-normal" value="">Categorías</option>
                            @foreach($categorias as $cat)
                                <option  value="{{ $cat->id }}">{{ $cat->title }}</option>
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
                                    placeholder={{ $sub->title }}
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
                    class="bg-[#E4002B] text-white px-[32px] h-[44px] rounded-[4px] text-[14px] font-inter uppercase cursor-pointer"
                >
                    Buscar
                </button>
            
                <button
                    wire:click="limpiarFiltros"
                    class="border border-[#E4002B] text-[#E4002B] px-[32px] h-[44px] rounded-[4px] text-[14px] font-inter uppercase cursor-pointer"
                >
                    Limpiar
                </button>
            </div>
        </div>
    </div>
    
    <div class="max-w-[1224px] mx-auto pt-[55px]">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[24px]">
            @foreach($categorias as $categoria)
                <button wire:click="seleccionarCategoria({{ $categoria->id }})"
                   class="group relative bg-white rounded-[4px] overflow-hidden transition-all duration-300 text-left cursor-pointer">
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
    </div>

    <livewire:vistas.nosotros.nosotros-home />
    <livewire:vistas.productos.productos-destacados/>
    <livewire:vistas.novedades.destacadas/>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoriaSelect = document.getElementById('categoriaSelect');
            const arrowCategoria = document.getElementById('arrowCategoria');
            
            const marcaSelect = document.getElementById('marcaSelect');
            const arrowMarca = document.getElementById('arrowMarca');
            const codigoSelect = document.getElementById('codigoSelect');
            const arrowCodigo = document.getElementById('arrowCodigo');

            const equivalenciaSelect = document.getElementById('equivalenciaSelect');
            const arrowEquivalencia = document.getElementById('arrowEquivalencia');

            if (codigoSelect && arrowCodigo) {
                codigoSelect.addEventListener('click', function() {
                    const isRotated = arrowCodigo.style.transform.includes('rotate(180deg)');
                    arrowCodigo.style.transform = isRotated ? 'rotate(0deg)' : 'rotate(180deg)';
                });
            }

            if (equivalenciaSelect && arrowEquivalencia) {
                equivalenciaSelect.addEventListener('click', function() {
                    const isRotated = arrowEquivalencia.style.transform.includes('rotate(180deg)');
                    arrowEquivalencia.style.transform = isRotated ? 'rotate(0deg)' : 'rotate(180deg)';
                });
            }
            
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