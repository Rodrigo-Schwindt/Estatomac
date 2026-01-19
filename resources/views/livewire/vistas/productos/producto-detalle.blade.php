<section class="max-w-[1224px] mx-auto" 
    x-data="{ 
        show: false, 
        showModal: false, 
        currentIndex: 0, 
        totalImages: {{ count(array_merge([$producto->image], $producto->gallery->pluck('image')->toArray())) }}
    }"
    x-init="setTimeout(() => show = true, 50)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 transform -translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0">
    
    <div class="max-w-[1224px] mx-auto pt-[24px] pb-[40px]">
        <nav class="text-black font-montserrat text-[12px] leading-[150%] flex items-center gap-1">
            <a wire:navigate href="{{ url('/') }}" class="text-black font-montserrat text-[12px] font-bold leading-[150%]">Inicio</a>
            <span class="text-black font-montserrat text-[12px] leading-[150%]">›</span>
            <span class="text-black font-montserrat text-[12px] leading-[150%]">{{ $producto->title }}</span>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-[25.5px]">
        <div>
            @php
                $allImages = array_merge([$producto->image], $producto->gallery->pluck('image')->toArray());
            @endphp

            <div class="border w-full md:w-[600px] h-[500px] border-[#E5E5E5] rounded-[4px] p-[24px] flex items-center justify-center mb-[16px] cursor-pointer group relative"
                 @click="showModal = true; currentIndex = {{ array_search($imagenActual, $allImages) !== false ? array_search($imagenActual, $allImages) : 0 }}">
                
                <img src="{{ asset('storage/' . $imagenActual) }}" alt="{{ $producto->title }}" class="h-full w-full object-contain" id="imagenPrincipal">
                
                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center"></div>
            </div>

            <div class="flex gap-[12px]">
                @foreach($allImages as $index => $img)
                    @if($index < 3)
                        <button wire:click="cambiarImagen('{{ $img }}')"
                                class="flex-shrink-0 w-[80px] h-[67px] cursor-pointer border-2 {{ $imagenActual === $img ? 'border-[#E40044]' : 'border-[#E5E5E5]' }} rounded-[4px] p-[8px] hover:border-[#E40044] transition-colors">
                            <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-contain">
                        </button>
                    @elseif($index === 3 && count($allImages) > 4)
                        <button @click="showModal = true; currentIndex = 3"
                                class="relative flex-shrink-0 w-[80px] cursor-pointer h-[67px] border-2 border-[#E5E5E5] rounded-[4px] p-[8px] overflow-hidden">
                            <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-contain blur-[1px]">
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                <span class="text-white font-bold">+{{ count($allImages) - 3 }}</span>
                            </div>
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="flex flex-col justify-between h-[500px]">
            <div>
                <p class="text-[#E40044] font-inter text-[16px] font-bold leading-[25px] pt-[21px] pb-[9px]">
                    {{ $producto->categoria ? $producto->categoria->title : 'Sin categoría' }}
                </p>
                <div class="h-[1px] bg-gray-200 w-full"></div>

                <h1 class="text-[#111010] font-inter text-[32px] font-semibold leading-tight uppercase mt-[36px] mb-[40px]">
                    {{ $producto->title }}
                </h1>

                <div class="space-y-0">
                    <div class="h-[1px] bg-gray-200 w-full"></div>
                    @foreach($producto->subcategorias as $subcategoria)
                        <div class="flex justify-between items-center px-[13px] py-[12px]">
                            <span class="text-[#111010] font-inter text-[16px] font-normal">{{ $subcategoria->title }}</span>
                            <span class="text-[#111010] font-inter text-[16px] font-bold">{{ $subcategoria->pivot->valor }}</span>
                        </div>
                        <div class="h-[1px] bg-gray-200 w-full"></div>
                    @endforeach
                </div>
            </div>

            <a href="/contacto" wire:navigate class="mt-[32px] inline-block w-full max-w-[280px] bg-[#E40044] text-white text-[14px] text-center font-semibold uppercase py-[14px] rounded-[4px] border border-transparent duration-300 hover:bg-white hover:border-[#E4002B] hover:text-[#E4002B]">
                Consultar
            </a>
        </div>
    </div>

    @if($relacionados->count())
        <div class="mt-[80px]">
            <h2 class="text-[#111010] font-inter text-[32px] font-semibold leading-[35px] mb-[32px]">Productos relacionados</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-[24px] mb-[144px]">
                @foreach($relacionados as $prodRel)
                <a wire:navigate href="{{ route('productos.detalle', $prodRel->id) }}" class="group relative bg-white overflow-hidden rounded-[4px] h-[312px] block">
                    <span class="absolute top-0 left-0 bg-[#E40044] text-white text-[12px] font-bold uppercase px-4 py-1 z-20">
                        {{ strtoupper($prodRel->nuevo) }}
                    </span>
                    
                    <div class="relative w-full h-[232px] flex items-center justify-center overflow-hidden rounded-[4px] border border-[#D9D9D9]">
                        <div class="absolute inset-0 bg-[#FFF5E6] opacity-0 group-hover:opacity-30 transition-opacity duration-300 z-10"></div>
                        
                        <img src="{{ asset('storage/' . $prodRel->image) }}" alt="{{ $prodRel->title }}" class="h-full w-full object-contain">
                    </div>

                    <div class="pt-[12px] px-[13px]">
                        <p class="text-[#E40044] font-inter text-[14px] font-bold uppercase">
                            {{ $prodRel->categoria ? $prodRel->categoria->title : '' }}
                        </p>
                        <h3 class="text-black font-inter text-[16px] font-normal uppercase line-clamp-2">{{ $prodRel->title }}</h3>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    @endif

{{-- Modal de Imágenes --}}
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
         @keydown.right.window="if(showModal) { currentIndex = (currentIndex + 1) % totalImages }"
         @keydown.left.window="if(showModal) { currentIndex = (currentIndex - 1 + totalImages) % totalImages }"
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/85 backdrop-blur-sm"
         style="display: none;">
        
        {{-- Backdrop clickeable para cerrar --}}
        <div @click="showModal = false" class="absolute inset-0"></div>

        {{-- Botón cerrar --}}
        <button @click="showModal = false" 
                class="absolute top-6 right-6 text-white cursor-pointer transition-transform p-2 z-[100] hover:scale-110">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Contenedor principal (relativo para estar sobre el backdrop) --}}
        <div class="relative w-full h-full flex flex-col items-center justify-center p-4 pointer-events-none">
            {{-- Imagen principal --}}
            <div class="relative w-full h-[70vh] flex items-center justify-center pointer-events-auto">
                @foreach($allImages as $index => $image)
                    <div x-show="currentIndex === {{ $index }}" 
                         x-transition:enter="transition-opacity duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="absolute inset-0 flex items-center justify-center"
                         style="display: none;">
                        <img src="{{ asset('storage/' . $image) }}" 
                             alt="Imagen {{ $index + 1 }}"
                             class="max-h-full max-w-full object-contain">
                    </div>
                @endforeach

                {{-- Botón anterior --}}
                <button @click.stop="currentIndex = (currentIndex - 1 + totalImages) % totalImages" 
                        class="absolute left-4 md:left-10 text-[60px] md:text-[80px] cursor-pointer p-4 md:p-10 rounded-full text-white hover:text-[#E40044] transition-all select-none leading-none z-10">
                    ‹
                </button>
                
                {{-- Botón siguiente --}}
                <button @click.stop="currentIndex = (currentIndex + 1) % totalImages" 
                        class="absolute right-4 md:right-10 text-[60px] md:text-[80px] cursor-pointer p-4 md:p-10 rounded-full text-white hover:text-[#E40044] transition-all select-none leading-none z-10">
                    ›
                </button>
            </div>

            {{-- Miniaturas --}}
            <div class="mt-8 flex gap-2 overflow-x-auto max-w-full p-2 pointer-events-auto">
                @foreach($allImages as $index => $image)
                    <button @click.stop="currentIndex = {{ $index }}" 
                            :class="currentIndex === {{ $index }} ? 'border-[#E40044] scale-110' : 'border-transparent opacity-50'"
                            class="w-16 h-16 border-2 rounded cursor-pointer transition-all duration-300 flex-shrink-0 bg-white p-1 hover:opacity-100">
                        <img src="{{ asset('storage/' . $image) }}" 
                             alt="Miniatura {{ $index + 1 }}"
                             class="w-full h-full object-contain">
                    </button>
                @endforeach
            </div>

            {{-- Contador --}}
            <div class="mt-4 text-white text-sm pointer-events-auto">
                <span x-text="currentIndex + 1"></span> / <span x-text="totalImages"></span>
            </div>
        </div>
    </div>
</template>
</section>