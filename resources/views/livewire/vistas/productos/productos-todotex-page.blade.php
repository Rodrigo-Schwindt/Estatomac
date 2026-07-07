<div class="min-h-screen">
    <div class="bg-[#F5F5F5] h-[150px] max-[639px]:h-[132px]">
        <div class="max-w-[1224px] mx-auto">
            <div class="max-[1199px]:px-4">
                <div class="max-w-[1224px] mx-auto pt-[16px] max-[639px]:pt-4">
                    <nav class="text-white font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] flex items-center gap-1">
                        <a wire:navigate href="{{ url('/') }}" class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] font-bold leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Inicio</a>
                        <span class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">|</span>
                        <span class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Productos</span>
                    </nav>
                </div>
            </div>
            <h1 class="text-[40px] max-[639px]:text-[32px] pt-[43px] max-[639px]:pt-[32px] font-bold max-[1199px]:px-4">Productos</h1>
        </div>
    </div>

    <div class="max-w-[1224px] mx-auto px-4 xl:px-0 pt-[79px] max-[1199px]:pt-8 max-[639px]:pt-6 pb-8 max-[639px]:pb-8">

        <div class="flex gap-[25px] items-start max-[1199px]:grid max-[1199px]:grid-cols-1 max-[1199px]:gap-8">
            
            <aside class="w-[300px] shrink-0 max-[1199px]:w-full todotex-appear">
                @php
                    $rubrosSectionOpen = filled($rubroId);
                @endphp

                <div class="todotex-filter-shell">
                    <div class="mb-4 min-[1200px]:hidden">
                        <p class="font-inter text-[13px] font-bold uppercase tracking-[0.16em] text-[#23378C]">Filtros</p>
                        <p class="font-inter text-[14px] leading-[150%] text-[#657084] mt-1">Buscar por codigo, familia o rubro.</p>
                    </div>

                    <div class="todotex-filter-search">
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Buscar..."
                               class="todotex-filter-search-input">
                        <button type="button" class="todotex-filter-search-button">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>

                    @php $todosActivo = !$familiaId && !$categoriaId && !$rubroId && !$search; @endphp
                    <button type="button"
                            wire:click="seleccionarTodos"
                            class="todotex-btn-todos {{ $todosActivo ? 'is-active' : '' }}">
                        Todos los productos
                    </button>

                    <div x-data="{ open: true }" class="todotex-filter-group">
                        <button type="button" @click="open = !open" class="todotex-filter-group-toggle">
                            <span>Familias</span>
                            <svg class="todotex-filter-group-icon" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" x-collapse class="todotex-filter-list">
                            @foreach($familias as $familia)
                                @php
                                    $isActiveFamilia = $familiaId == $familia->id;
                                    $hasActiveCategoriaFamilia = $familia->categorias->contains('id', $categoriaId);
                                    $isOpenFamilia = $isActiveFamilia || $hasActiveCategoriaFamilia;
                                @endphp
                                <div x-data="{ open: {{ $isOpenFamilia ? 'true' : 'false' }} }" class="todotex-filter-item">
                                    <button type="button"
                                            @click="open = !open"
                                            wire:click="seleccionarFamilia({{ $familia->id }})"
                                            class="todotex-filter-item-button {{ $isActiveFamilia || $hasActiveCategoriaFamilia ? 'is-active' : '' }}">
                                        <span>{{ $familia->titulo }}</span>
                                        <svg class="todotex-filter-item-icon" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    @if($familia->categorias->isNotEmpty())
                                        <div x-show="open" x-collapse class="todotex-filter-children">
                                            @foreach($familia->categorias as $categoria)
                                                <button wire:click="seleccionarCategoria({{ $categoria->id }})"
                                                        class="todotex-filter-child {{ $categoriaId == $categoria->id ? 'is-active' : '' }}">
                                                    {{ $categoria->titulo }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div x-data="{ open: {{ $rubrosSectionOpen ? 'true' : 'false' }} }" class="todotex-filter-group">
                        <button type="button" @click="open = !open" class="todotex-filter-group-toggle">
                            <span>Rubros</span>
                            <svg class="todotex-filter-group-icon" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" x-collapse class="todotex-filter-list">
                            @foreach($rubros as $rubro)
                                <button type="button"
                                        wire:click="seleccionarRubro({{ $rubro->id }})"
                                        class="todotex-filter-item-button {{ $rubroId == $rubro->id ? 'is-active' : '' }}">
                                    <span>{{ $rubro->titulo }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>

            <div class="flex-1 min-w-0 max-[1199px]:w-full todotex-appear tdx-products-area"
                 wire:loading.class="tdx-products-fading"
                 wire:target="seleccionarFamilia,seleccionarCategoria,seleccionarRubro,search">

                {{-- DETALLE --}}
                @if($producto)
                    @php
                        $portadaDetalle = $producto->gallery->firstWhere('portada', true) ?? $producto->gallery->first();
                        $familiaDetalle = $producto->categorias->first()?->familia;
                    @endphp
                    @php
                        $galleryUrls = $producto->gallery->map(fn($img) => Storage::url($img->image))->values();
                        $portadaUrl = $portadaDetalle ? Storage::url($portadaDetalle->image) : ($galleryUrls->first() ?? '');
                        $portadaIndex = $galleryUrls->search($portadaUrl) ?: 0;
                        $totalGallery = $galleryUrls->count();
                    @endphp
                    <div x-data="{
                        activeImg: '{{ $portadaUrl }}',
                        currentIndex: {{ $portadaIndex }},
                        showModal: false,
                        totalImages: {{ $totalGallery }},
                        images: @js($galleryUrls),
                        setImage(index) {
                            this.currentIndex = index;
                            this.activeImg = this.images[index];
                        }
                    }">

                        <div class="flex gap-8 items-stretch max-[1199px]:flex-col">

                            <!-- Galería izquierda -->
                            <div class="flex flex-col gap-3 w-[392px] max-[1199px]:w-full shrink-0">
                                <!-- Imagen principal (click abre modal) -->
                                <div class="w-full aspect-square h-[392px] max-[1199px]:h-auto rounded-[8px] border border-[#EAEAEA] overflow-hidden relative cursor-pointer"
                                     data-edge-bg-target
                                     @click="showModal = true">
                                    <img :src="activeImg"
                                         alt="{{ $producto->codigo }}"
                                         data-edge-bg-image
                                         class="w-full h-full object-cover ">
                                    <div class="absolute inset-0 bg-gray-400/10 pointer-events-none"></div>
                                </div>

                                <!-- Thumbnails: máx 3, el 3ro oscurecido con +N -->
                                @if($totalGallery > 1)
                                    <div class="flex gap-3">
                                        @foreach($galleryUrls as $idx => $url)
                                            @if($idx < 2)
                                                <button type="button"
                                                        @click="setImage({{ $idx }})"
                                                        :class="currentIndex === {{ $idx }} ? 'border-[#018637] border-2' : 'border-[#EAEAEA] border'"
                                                        class="w-[72px] h-[72px] rounded-[6px] overflow-hidden transition-all cursor-pointer relative shrink-0"
                                                        data-edge-bg-target>
                                                    <img src="{{ $url }}" alt="" data-edge-bg-image class="w-full h-full object-cover ">
                                                    <div class="absolute inset-0 bg-gray-400/10 pointer-events-none"></div>
                                                </button>
                                            @elseif($idx === 2)
                                                <button type="button"
                                                        @click="{{ $totalGallery > 3 ? 'showModal = true; currentIndex = 2' : 'setImage(2)' }}"
                                                        :class="currentIndex === 2 && !{{ $totalGallery > 3 ? 'true' : 'false' }} ? 'border-[#018637] border-2' : 'border-[#EAEAEA] border'"
                                                        class="w-[72px] h-[72px] rounded-[6px] overflow-hidden transition-all cursor-pointer relative shrink-0 border border-[#EAEAEA]"
                                                        data-edge-bg-target>
                                                    <img src="{{ $url }}" alt="" data-edge-bg-image class="w-full h-full object-cover  {{ $totalGallery > 3 ? 'blur-[1px]' : '' }}">
                                                    @if($totalGallery > 3)
                                                        <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                                            <span class="text-white font-semibold text-[18px]">+{{ $totalGallery - 2 }}</span>
                                                        </div>
                                                    @else
                                                        <div class="absolute inset-0 bg-gray-400/10 pointer-events-none"></div>
                                                    @endif
                                                </button>
                                                @break
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <!-- Info derecha -->
                            <div class="flex-1 min-w-0 flex flex-col justify-between h-[470px] max-[1199px]:h-auto max-[1199px]:gap-8">
                                <div>

                                <div class="flex items-center justify-between gap-2 border-b border-[#EAEAEA] pb-[5px] max-[639px]:flex-col max-[639px]:items-start max-[639px]:gap-3 max-[639px]:pb-[8px]">
                                    @if($producto->codigo)
                                        <span class="text-[#018637] font-inter text-[18px] max-[639px]:text-[15px] font-bold uppercase tracking-wide">
                                            COD. {{ $producto->codigo }}
                                        </span>
                                    @endif
                                    @if($familiaDetalle)
                                        <span class="text-[#23378C] font-inter text-[18px] max-[639px]:text-[15px] font-bold uppercase tracking-wide shrink-0">
                                            {{ $familiaDetalle->titulo }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Título -->
                                <h2 class="text-[#131313] font-inter text-[32px] max-[1199px]:text-[28px] max-[639px]:text-[23px] font-semibold leading-snug pt-[9px] max-[639px]:pt-[12px]">
                                    {!! strip_tags($producto->descripcion) !!}
                                </h2>

                                <!-- Características -->
                                @if($producto->presentacion)
                                    <div class="pt-[69px] max-[1199px]:pt-8 max-[639px]:pt-6">
                                        <p class="font-inter text-[18px] max-[639px]:text-[16px] font-bold text-[#131313] mb-3">Características</p>
                                        <table class="w-full border-collapse">
                                            <tr class="border-y border-[#EAEAEA]">
                                                <td class="py-2 max-[639px]:py-3 pr-4 font-inter px-[16px] max-[639px]:px-2 text-[16px] max-[639px]:text-[14px] text-[#131313] w-1/2">Presentación</td>
                                                <td class="py-2 max-[639px]:py-3 font-inter text-[16px] max-[639px]:text-[14px] px-[16px] max-[639px]:px-2 text-[#131313] font-semibold text-right">{!! strip_tags($producto->presentacion) !!}</td>
                                            </tr>
                                        </table>
                                    </div>
                                @endif

                                </div>

                                <!-- Botón Consultar -->
                                <a href="{{ route('contacto') }}?producto={{ $producto->codigo }}"
                                   class="w-full h-[40px] max-[1199px]:h-[44px] rounded-full max-[1199px]:rounded-[8px] bg-[#23378C] flex items-center justify-center
                                          font-inter text-white text-[16px] font-semibold
                                          hover:bg-[#1a2a6e] transition-colors">
                                    Consultar
                                </a>
                            </div>
                        </div>
                        <!-- Modal galería -->
                        <template x-teleport="body">
                            <div x-show="showModal"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 @keydown.escape.window="showModal = false"
                                 @keydown.right.window="if(showModal) { currentIndex = (currentIndex + 1) % totalImages; activeImg = images[currentIndex]; }"
                                 @keydown.left.window="if(showModal) { currentIndex = (currentIndex - 1 + totalImages) % totalImages; activeImg = images[currentIndex]; }"
                                 class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/85 backdrop-blur-sm"
                                 style="display: none;">

                                <div @click="showModal = false" class="absolute inset-0"></div>

                                <button @click="showModal = false"
                                        class="absolute top-6 right-6 text-white cursor-pointer p-2 z-[100] hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>

                                <div class="relative w-full h-full flex flex-col items-center justify-center p-4 pointer-events-none">
                                    <div class="relative w-full h-[70vh] flex items-center justify-center pointer-events-auto">
                                        @foreach($galleryUrls as $idx => $url)
                                            <div x-show="currentIndex === {{ $idx }}"
                                                 x-transition:enter="transition-opacity duration-300"
                                                 x-transition:enter-start="opacity-0"
                                                 x-transition:enter-end="opacity-100"
                                                 class="absolute inset-0 flex items-center justify-center px-16"
                                                 style="display: none;">
                                                <img src="{{ $url }}" alt="{{ $idx + 1 }}" class="max-h-full max-w-full object-cover">
                                            </div>
                                        @endforeach

                                        <button @click.stop="currentIndex = (currentIndex - 1 + totalImages) % totalImages; activeImg = images[currentIndex];"
                                                class="absolute left-4 text-[60px] cursor-pointer p-4 rounded-full text-white hover:text-[#018637] transition-all select-none leading-none z-10">
                                            ‹
                                        </button>
                                        <button @click.stop="currentIndex = (currentIndex + 1) % totalImages; activeImg = images[currentIndex];"
                                                class="absolute right-4 text-[60px] cursor-pointer p-4 rounded-full text-white hover:text-[#018637] transition-all select-none leading-none z-10">
                                            ›
                                        </button>
                                    </div>

                                    <div class="mt-6 flex gap-2 overflow-x-auto max-w-full p-2 pointer-events-auto scrollbar-hide">
                                        @foreach($galleryUrls as $idx => $url)
                                            <button @click.stop="currentIndex = {{ $idx }}; activeImg = images[{{ $idx }}];"
                                                    :class="currentIndex === {{ $idx }} ? 'border-[#018637] scale-110' : 'border-transparent opacity-50'"
                                                    class="w-16 h-16 border-2 rounded cursor-pointer transition-all duration-300 shrink-0 p-1 hover:opacity-100"
                                                    data-edge-bg-target>
                                                <img src="{{ $url }}" alt="Miniatura {{ $idx + 1 }}" data-edge-bg-image class="w-full h-full object-cover">
                                            </button>
                                        @endforeach
                                    </div>

                                    <div class="mt-4 text-white text-sm pointer-events-auto">
                                        <span x-text="currentIndex + 1"></span> / <span x-text="totalImages"></span>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>

                    {{-- RELACIONADOS --}}
                    @if($relacionados->isNotEmpty())
                        <div class="mt-[60px]">
                            <h2 class="text-[#131313] font-inter text-[24px] font-bold mb-6">Productos relacionados</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 max-[1199px]:grid-cols-2 max-[639px]:!grid-cols-1 gap-4 max-[1199px]:gap-5 todotex-appear-list">
                                @foreach($relacionados as $rel)
                                    @php
                                        $relPortada = $rel->gallery->firstWhere('portada', true) ?? $rel->gallery->first();
                                        $relFamilia = $rel->categorias->first()?->familia;
                                    @endphp
                                    <button wire:click="verProducto({{ $rel->id }})"
                                            class="bg-white rounded-[4px] max-[1199px]:rounded-[8px] border border-[#EAEAEA] min-h-[400px] max-[639px]:min-h-0 overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col text-left cursor-pointer w-full">
                                        <div class="w-full overflow-hidden relative h-[248px] max-[639px]:h-[210px]" data-edge-bg-target>
                                            @if($relPortada)
                                                <img src="{{ Storage::url($relPortada->image) }}"
                                                     alt="{{ $rel->codigo }}"
                                                     data-edge-bg-image
                                                     class="w-full h-full object-cover ">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                    <div class="absolute inset-0 bg-gray-400/10 pointer-events-none"></div>
                                        </div>
                                        <div class="pt-[22px] px-[18px] pb-[15px] max-[1199px]:pb-[18px] flex flex-col gap-[16px]">
                                            <div class="flex items-center justify-between gap-2 max-[639px]:items-start">
                                                @if($rel->codigo)
                                                    <span class="text-[#018637] font-inter text-[16px] max-[1199px]:text-[15px] max-[639px]:text-[13px] font-bold uppercase tracking-wide">COD.{{ $rel->codigo }}</span>
                                                @else
                                                    <span></span>
                                                @endif
                                                @if($relFamilia)
                                                    <span class="text-[#23378C] font-inter text-[16px] max-[1199px]:text-[15px] max-[639px]:text-[13px] font-bold uppercase tracking-wide shrink-0 max-[639px]:text-right">{{ $relFamilia->titulo }}</span>
                                                @endif
                                            </div>
                                            <p class="text-[#131313] font-inter text-[21px] max-[639px]:text-[18px] font-bold leading-snug line-clamp-2">
                                                {!! strip_tags($rel->descripcion) !!}
                                            </p>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                {{-- LISTADO --}}
                @elseif($productos->isEmpty())
                    @if($bannerImagen)
                        <div class="w-full mb-6 rounded-[8px] overflow-hidden">
                            <img src="{{ Storage::url($bannerImagen) }}" alt="" class="w-full max-h-[280px] object-cover">
                        </div>
                    @endif
                    <div class="flex flex-col items-center justify-center py-20 max-[639px]:py-14 text-gray-400">
                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-lg font-medium text-gray-500">No se encontraron productos</p>
                    </div>
                @else
                    @if($bannerImagen)
                        <div class="w-full mb-6 rounded-[8px] overflow-hidden">
                            <img src="{{ Storage::url($bannerImagen) }}" alt="" class="w-full max-h-[280px] object-cover">
                        </div>
                    @endif
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 max-[1199px]:grid-cols-2 max-[639px]:!grid-cols-1 gap-4 max-[1199px]:gap-5 todotex-appear-list">
                        @foreach($productos as $p)
                            @php
                                $portada = $p->gallery->firstWhere('portada', true) ?? $p->gallery->first();
                                $familia = $p->categorias->first()?->familia;
                            @endphp
                            <button wire:click="verProducto({{ $p->id }})"
                                    class="bg-white rounded-[4px] max-[1199px]:rounded-[8px] border border-[#EAEAEA] min-h-[400px] max-[639px]:min-h-0 overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col text-left cursor-pointer w-full">

                                <div class="w-full overflow-hidden relative h-[248px] max-[639px]:h-[210px]" data-edge-bg-target>
                                    @if($portada)
                                        <img src="{{ Storage::url($portada->image) }}"
                                             alt="{{ $p->codigo }}"
                                             data-edge-bg-image
                                             class="w-full h-full object-cover ">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gray-400/10 pointer-events-none"></div>
                                </div>

                                <div class="pt-[22px] px-[18px] pb-[15px] max-[1199px]:pb-[18px] flex flex-col gap-[16px] flex-1">
                                    <div class="flex items-center justify-between gap-2 max-[639px]:items-start">
                                        @if($p->codigo)
                                            <span class="text-[#018637] font-inter text-[16px] max-[1199px]:text-[15px] max-[639px]:text-[13px] font-bold leading-normal uppercase tracking-wide">
                                                COD.{{ $p->codigo }}
                                            </span>
                                        @else
                                            <span></span>
                                        @endif
                                        @if($familia)
                                            <span class="text-[#23378C] font-inter text-[16px] max-[1199px]:text-[15px] max-[639px]:text-[13px] font-bold uppercase tracking-wide shrink-0 max-[639px]:text-right">
                                                {{ $familia->titulo }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[#131313] font-inter text-[21px] max-[639px]:text-[18px] font-bold leading-snug line-clamp-3">
                                        {!! strip_tags($p->descripcion) !!}
                                    </p>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    @if($productos->hasPages())
                        <nav class="tdx-pag">
                            <div class="tdx-pag-btns">
                                @if($productos->onFirstPage())
                                    <span class="tdx-btn tdx-btn-nav tdx-btn-dis">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </span>
                                @else
                                    <button wire:click="previousPage" class="tdx-btn tdx-btn-nav" aria-label="Anterior">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                @endif

                                @php
                                    $currentPage = $productos->currentPage();
                                    $lastPage    = $productos->lastPage();
                                    $pages = [];
                                    for ($i = 1; $i <= $lastPage; $i++) {
                                        if ($i === 1 || $i === $lastPage || ($i >= $currentPage - 2 && $i <= $currentPage + 2)) {
                                            $pages[] = $i;
                                        }
                                    }
                                @endphp
                                @foreach($pages as $idx => $page)
                                    @if($idx > 0 && $pages[$idx - 1] !== $page - 1)
                                        <span class="tdx-dots">…</span>
                                    @endif
                                    @if($page === $currentPage)
                                        <span class="tdx-btn tdx-btn-active" aria-current="page">{{ $page }}</span>
                                    @else
                                        <button wire:click="gotoPage({{ $page }})" class="tdx-btn tdx-btn-num">{{ $page }}</button>
                                    @endif
                                @endforeach

                                @if($productos->hasMorePages())
                                    <button wire:click="nextPage" class="tdx-btn tdx-btn-nav" aria-label="Siguiente">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                @else
                                    <span class="tdx-btn tdx-btn-nav tdx-btn-dis">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </span>
                                @endif
                            </div>
                            <p class="tdx-pag-info">Mostrando <strong>{{ $productos->firstItem() }}</strong>–<strong>{{ $productos->lastItem() }}</strong> de <strong>{{ $productos->total() }}</strong> productos</p>
                        </nav>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <style>
        /* ── Transición filtros ── */
        .tdx-products-area {
            transition: opacity 0.2s ease, filter 0.2s ease;
        }
        .tdx-products-fading {
            opacity: 0.3;
            filter: blur(3px);
            pointer-events: none;
        }

        /* ── Paginado ── */
        .tdx-pag {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            padding-top: 36px;
        }
        .tdx-pag-btns {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .tdx-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 6px;
            border-radius: 8px;
            border: 1.5px solid #dde3ea;
            background: #fff;
            color: #444;
            font-family: Inter, sans-serif;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s, color 0.15s, border-color 0.15s;
        }
        .tdx-btn-nav {
            color: #23378C;
        }
        .tdx-btn-num:hover,
        .tdx-btn-nav:hover {
            background: #23378C;
            border-color: #23378C;
            color: #fff;
        }
        .tdx-btn-active {
            background: #23378C;
            border-color: #23378C;
            color: #fff;
            font-weight: 700;
            pointer-events: none;
        }
        .tdx-btn-dis {
            color: #c0c8d4;
            background: #f5f6f8;
            border-color: #e8eaed;
            pointer-events: none;
            cursor: default;
        }
        .tdx-dots {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 40px;
            color: #8b9ab0;
            font-family: Inter, sans-serif;
            font-size: 14px;
        }
        .tdx-pag-info {
            font-family: Inter, sans-serif;
            font-size: 13px;
            color: #8b9ab0;
            margin: 0;
        }
        @media (max-width: 639px) {
            .tdx-btn {
                min-width: 36px;
                height: 36px;
                font-size: 13px;
                border-radius: 7px;
            }
            .tdx-pag-btns { gap: 4px; }
        }

        /* ── Filtros ── */
        .todotex-filter-shell {
            background: #fff;
        }

        .todotex-filter-search {
            display: flex;
            align-items: stretch;
            height: 50px;
            overflow: hidden;
            border: 1px solid #98a4b5;
            border-radius: 16px;
            background: #fff;
        }

        .todotex-filter-search-input {
            flex: 1;
            min-width: 0;
            padding: 0 18px;
            border: 0;
            background: transparent;
            color: #181818;
            font-family: Inter, sans-serif;
            font-size: 16px;
            line-height: 1;
        }

        .todotex-filter-search-input::placeholder {
            color: #8b8b8b;
        }

        .todotex-filter-search-input:focus {
            outline: none;
        }

        .todotex-filter-search-button {
            display: flex;
            width: 48px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border: 0;
            background: #018637;
            color: #fff;
            cursor: pointer;
        }

        .todotex-btn-todos {
            display: flex;
            width: 100%;
            align-items: center;
            padding: 8px 0;
            padding-top: 18px;
            border: 0;
            border-bottom: 1px solid #EAEAEA;
            background: transparent;
            color: #888;
            font-family: Inter, sans-serif;
            font-size: 19px;
            font-weight: 400;
            text-align: left;
            cursor: pointer;
            transition: color .2s ease;
        }

        .todotex-btn-todos:hover,
        .todotex-btn-todos.is-active {
            color: #018637;
        }

        .todotex-btn-todos.is-active {
            font-weight: 700;
        }

        .todotex-filter-group {
            margin-top: 18px;
        }

        .todotex-filter-group-toggle {
            display: flex;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 0 5px;
            border-bottom: 1px solid #EAEAEA;
            font-size: 20px;
            font-weight: 700;
            color: #018637;
            text-align: left;
            cursor: pointer;
        }

        .todotex-filter-group-icon,
        .todotex-filter-item-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            color: #c8ccd3;
            transition: transform .2s ease;
        }

        .todotex-filter-item {
            border-bottom: 1px solid #ececec;
        }

        .todotex-filter-item-button {
            display: flex;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 0;
            border: 0;
            background: transparent;
            color: #1b1b1b;
            font-family: Inter, sans-serif;
            font-size: 16px;
            font-weight: 400;
            text-align: left;
            cursor: pointer;
            transition: color .2s ease;
        }

        .todotex-filter-item-button.is-active {
            font-weight: 700;
            color: #111111;
        }

        .todotex-filter-children {
            display: grid;
            gap: 10px;
            padding: 0 0 14px 18px;
        }

        .todotex-filter-child {
            width: 100%;
            border: 0;
            background: transparent;
            color: #181818;
            font-family: Inter, sans-serif;
            font-size: 15px;
            font-weight: 400;
            text-align: left;
            cursor: pointer;
            transition: color .2s ease;
        }

        .todotex-filter-child.is-active,
        .todotex-filter-child:hover,
        .todotex-filter-item-button:hover {
            color: #018637;
        }

        @keyframes todotexFadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1199px) {
            .todotex-filter-shell {
                padding: 20px;
                border: 1px solid #dde3ea;
                border-radius: 14px;
                box-shadow: 0 18px 45px rgba(10, 19, 48, 0.08);
            }

            .todotex-filter-search {
                height: 48px;
                border-radius: 12px;
            }

            .todotex-filter-group {
                margin-top: 22px;
            }

            .todotex-filter-group-toggle {
                font-size: 16px;
            }

            .todotex-filter-item-button {
                padding: 12px 0;
                font-size: 15px;
            }

            .todotex-filter-children {
                gap: 8px;
                padding-left: 14px;
            }

            .todotex-filter-child {
                font-size: 14px;
            }

            .todotex-appear {
                animation: todotexFadeUp .5s ease both;
            }

            .todotex-appear-list > * {
                animation: todotexFadeUp .45s ease both;
            }

            .todotex-appear-list > *:nth-child(2) {
                animation-delay: .04s;
            }

            .todotex-appear-list > *:nth-child(3) {
                animation-delay: .08s;
            }

            .todotex-appear-list > *:nth-child(4) {
                animation-delay: .12s;
            }

            .todotex-appear-list > *:nth-child(5) {
                animation-delay: .16s;
            }

            .todotex-appear-list > *:nth-child(n+6) {
                animation-delay: .2s;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .todotex-appear,
            .todotex-appear-list > * {
                animation: none;
            }
        }
    </style>

    @include('partials.todotex-edge-backgrounds')
</div>
