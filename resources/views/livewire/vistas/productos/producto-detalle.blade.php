<section class="max-w-[1224px] mx-auto max-[1199px]:px-4" 
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
    
    <div class="max-w-[1224px] mx-auto pt-[24px] pb-[40px] max-[1199px]:pt-5 max-[1199px]:pb-8 max-[639px]:pt-4 max-[639px]:pb-6">
        <nav class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] flex items-center gap-1">
            <a wire:navigate href="{{ url('/') }}" class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] font-bold leading-[150%]">Inicio</a>
            <span class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%]">›</span>
            <span class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] truncate">{{ $producto->title }}</span>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-[25.5px] max-[1199px]:gap-6 max-[639px]:gap-5">
        <div>
            @php
                $allImages = array_merge([$producto->image], $producto->gallery->pluck('image')->toArray());
            @endphp

            <div class="border w-full md:w-[600px] max-[1199px]:w-full h-[500px] max-[1199px]:h-[400px] max-[767px]:h-[350px] max-[639px]:h-[300px] border-[#E5E5E5] rounded-[4px] p-[24px] max-[639px]:p-4 flex items-center justify-center mb-[16px] max-[639px]:mb-3 cursor-pointer group relative"
                 data-edge-bg-target
                 @click="showModal = true; currentIndex = {{ array_search($imagenActual, $allImages) !== false ? array_search($imagenActual, $allImages) : 0 }}">

                <span class="absolute top-0 left-0 {{ $producto->nuevo === 'nuevo' ? 'bg-[#E40044]' : 'bg-[#111]' }} text-white text-[12px] items-center justify-center font-semibold uppercase w-[115px] h-[37px] text-center font-inter text-[14px] font-bold leading-[30px] z-20 shadow-md">
                    <p class="pt-1">{{ $producto->nuevo === 'nuevo' ? 'NUEVO' : 'RECONSTRUIDO' }}</p>
                </span>
                <img src="{{ asset('storage/' . $imagenActual) }}" 
                     alt="{{ $producto->title }}" 
                     data-edge-bg-image
                     class="h-full w-full object-contain transition-transform duration-300" 
                     id="imagenPrincipal">
                
                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center"></div>
            </div>

            <div class="flex gap-[12px] max-[639px]:gap-2 overflow-x-auto pb-2">
                @foreach($allImages as $index => $img)
                    @if($index < 3)
                        <button wire:click="cambiarImagen('{{ $img }}')"
                                class="flex-shrink-0 w-[80px] h-[67px] max-[639px]:w-[70px] max-[639px]:h-[58px] cursor-pointer border-2 {{ $imagenActual === $img ? 'border-[#E40044]' : 'border-[#E5E5E5]' }} rounded-[4px] p-[8px] max-[639px]:p-[6px] hover:border-[#E40044] transition-all duration-200"
                                data-edge-bg-target>
                            <img src="{{ asset('storage/' . $img) }}" data-edge-bg-image class="w-full h-full object-contain">
                        </button>
                    @elseif($index === 3 && count($allImages) > 4)
                        <button @click="showModal = true; currentIndex = 3"
                                class="relative flex-shrink-0 w-[80px] max-[639px]:w-[70px] cursor-pointer h-[67px] max-[639px]:h-[58px] border-2 border-[#E5E5E5] rounded-[4px] p-[8px] max-[639px]:p-[6px] overflow-hidden transition-transform duration-200"
                                data-edge-bg-target>
                            <img src="{{ asset('storage/' . $img) }}" data-edge-bg-image class="w-full h-full object-contain blur-[1px]">
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                <span class="text-white font-bold text-sm max-[639px]:text-xs">+{{ count($allImages) - 3 }}</span>
                            </div>
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="flex flex-col justify-between h-[500px] max-[1199px]:h-auto max-[1199px]:mt-6">
            <div>
                <p class="text-[#E40044] font-inter text-[16px] max-[639px]:text-[14px] uppercase font-bold leading-[25px] max-[639px]:leading-[22px] pt-[21px] max-[1199px]:pt-0 pb-[9px] max-[639px]:pb-2">
                    {{ $producto->categoria ? $producto->categoria->title : '' }}
                </p>
                <div class="h-[1px] bg-gray-200 w-full"></div>

                <h1 class="text-[#111010] font-inter text-[32px] max-[1199px]:text-[28px] max-[767px]:text-[24px] max-[639px]:text-[22px] font-semibold leading-tight uppercase mt-[36px] max-[1199px]:mt-6 max-[639px]:mt-5 mb-[40px] max-[1199px]:mb-8 max-[639px]:mb-6">
                    {{ $producto->title }}
                </h1>

                <div class="space-y-0">
                    <div class="h-[1px] bg-gray-200 w-full"></div>
                    @foreach($producto->subcategorias as $subcategoria)
                        <div class="flex justify-between items-center px-[13px] max-[639px]:px-2 py-[12px] max-[639px]:py-3 transition-colors duration-200">
                            <span class="text-[#111010] font-inter text-[16px] max-[639px]:text-[14px] font-normal">{{ $subcategoria->title }}</span>
                            <span class="text-[#111010] font-inter text-[16px] max-[639px]:text-[14px] font-bold">{{ $subcategoria->pivot->valor }}</span>
                        </div>
                        <div class="h-[1px] bg-gray-200 w-full"></div>
                    @endforeach
                </div>
            </div>

            <a href="/contacto" 
               wire:navigate 
               class="mt-[32px] max-[1199px]:mt-8 max-[639px]:mt-6 inline-block w-full max-w-[280px] max-[1199px]:max-w-full bg-[#E40044] text-white text-[14px] max-[639px]:text-[13px] text-center font-semibold uppercase py-[14px] max-[639px]:py-3 rounded-[4px] border border-transparent duration-300 hover:bg-white hover:border-[#E4002B] hover:text-[#E4002B] transition-all">
                Consultar
            </a>
        </div>
    </div>
    @if($partesRelacionadas->count() > 0)
<div class="mt-[80px] max-[1199px]:mt-16 max-[639px]:mt-12"
     x-data="{ show: false }"
     x-intersect.once.threshold.0.1="show = true">
    
    <div class="flex items-center justify-between mb-[32px] max-[1199px]:mb-6 max-[639px]:mb-5 transition-all duration-700 ease-out transform"
         :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
        <h2 class="text-[#111010] font-inter text-[32px] max-[1199px]:text-[28px] max-[767px]:text-[24px] max-[639px]:text-[22px] font-semibold leading-[35px] max-[639px]:leading-[28px]">
            Partes relacionadas
        </h2>
    </div>

    <div class="relative group/slider"
         x-show="show"
         x-transition:enter="transition ease-out duration-1000 delay-200"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0">
         
        <div class="swiper partesRelacionadasSwiper !pb-4 md:!pb-10 !px-1">
            <div class="swiper-wrapper">
                @foreach($partesRelacionadas as $parte)
                <div class="swiper-slide transition-transform duration-300">
                    <a wire:navigate href="{{ route('productos.detalle', $parte->id) }}" class="group relative bg-white overflow-hidden rounded-[4px] h-[312px] block transition-all duration-300 w-full">

                        <span class="absolute top-0 left-0 {{ $producto->nuevo === 'nuevo' ? 'bg-[#E40044]' : 'bg-[#111]' }} text-white text-[12px] items-center justify-center font-semibold uppercase w-[115px] h-[37px] text-center font-inter text-[14px] font-bold leading-[30px] z-20 shadow-md">
                            <p class="pt-1">{{ $producto->nuevo === 'nuevo' ? 'NUEVO' : 'RECONSTRUIDO' }}</p>
                        </span>
                    
                        <div class="overflow-hidden rounded-[4px] border border-[#D9D9D9] relative w-full h-[232px] flex items-center justify-center overflow-hidden rounded-[4px]" data-edge-bg-target>
                            
                            <div class="absolute inset-0 bg-[#FFF5E6] opacity-0 group-hover:opacity-30 transition-opacity duration-500 z-10"></div>
                            
                            <img
                                src="{{ asset('storage/' . $parte->image) }}"
                                alt="{{ $parte->title }}"
                                data-edge-bg-image
                                class="h-full w-full object-contain p-4 transition-transform duration-700 group-hover:scale-110"
                            >
                        </div>
                    
                        <div class="pt-[12px] px-[13px]">
                            <p class="text-[#E40044] font-inter text-[14px] font-bold uppercase leading-[25px] pb-[2px] tracking-wide">
                                {{ $parte->categoria ? $parte->categoria->title : '' }}
                            </p>
                    
                            <h3 class="text-black font-inter text-[16px] font-normal uppercase leading-[22px] line-clamp-2 transition-colors duration-300">
                                {{ $parte->title }}
                            </h3>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    .partesRelacionadasSwiper {
        overflow: hidden;
        position: relative;
    }

    .partesRelacionadasSwiper .swiper-slide {
        width: 286px;
        flex-shrink: 0;
        height: auto; 
    }

    @media (max-width: 1199px) {
        .partesRelacionadasSwiper .swiper-slide {
            width: 270px;
        }
    }

    @media (max-width: 767px) {
        .partesRelacionadasSwiper .swiper-slide {
            width: 100% !important;
        }
    }
    
    .partesRelacionadasSwiper .swiper-button-disabled {
        opacity: 0.35 !important;
        pointer-events: none;
        cursor: not-allowed;
    }
</style>

<script>
(function() {
    let partesRelacionadasSwiperInstance = null;

    function initPartesRelacionadasSwiper() {
        if (partesRelacionadasSwiperInstance) {
            partesRelacionadasSwiperInstance.destroy(true, true);
            partesRelacionadasSwiperInstance = null;
        }

        const swiperElement = document.querySelector('.partesRelacionadasSwiper');
        if (!swiperElement) return;

        setTimeout(() => {
            partesRelacionadasSwiperInstance = new Swiper('.partesRelacionadasSwiper', {
                slidesPerView: 'auto',
                spaceBetween: 24,
                speed: 600, 
                loop: true,
                grabCursor: true,
                watchOverflow: true,
                autoplay: {
                    delay: 2200,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                breakpoints: {
                    0: {
                        slidesPerView: 1,
                        centeredSlides: false,
                        spaceBetween: 16,
                    },
                    540: {
                        slidesPerView: 'auto',
                        centeredSlides: false,
                        spaceBetween: 16,
                    },
                    768: {
                        slidesPerView: 'auto',
                        spaceBetween: 20,
                    },
                    1200: {
                        slidesPerView: 'auto',
                        spaceBetween: 24,
                    }
                }
            });
        }, 150);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPartesRelacionadasSwiper);
    } else {
        initPartesRelacionadasSwiper();
    }

    document.addEventListener('livewire:navigated', initPartesRelacionadasSwiper);
    
    document.addEventListener('livewire:navigating', () => {
        if (partesRelacionadasSwiperInstance) {
            partesRelacionadasSwiperInstance.destroy(true, true);
            partesRelacionadasSwiperInstance = null;
        }
    });
})();
</script>
@endif

    @if($relacionados->count())
        <div class="mt-[20px] max-[1199px]:mt-16 max-[639px]:mt-12">
            <h2 class="text-[#111010] font-inter text-[32px] max-[1199px]:text-[28px] max-[767px]:text-[24px] max-[639px]:text-[22px] font-semibold leading-[35px] max-[639px]:leading-[28px] mb-[32px] max-[1199px]:mb-6 max-[639px]:mb-5">
                Productos relacionados
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-[24px] max-[1199px]:grid-cols-3 max-[991px]:grid-cols-2 max-[639px]:grid-cols-1 max-[1199px]:gap-5 mb-[40px] max-[1199px]:mb-[40px] max-[639px]:mb-16">
                @foreach($relacionados as $prodRel)
                <a wire:navigate 
                   href="{{ route('productos.detalle', $prodRel->id) }}" 
                   class="group relative bg-white overflow-hidden rounded-[4px] h-[312px] max-[1199px]:h-auto block transition-all duration-300">
                   <span class="absolute top-0 left-0 {{ $producto->nuevo === 'nuevo' ? 'bg-[#E40044]' : 'bg-[#111]' }} text-white text-[12px] items-center justify-center font-semibold uppercase w-[115px] h-[37px] text-center font-inter text-[14px] font-bold leading-[30px] z-20 shadow-md">
                    <p class="pt-1">{{ $producto->nuevo === 'nuevo' ? 'NUEVO' : 'RECONSTRUIDO' }}</p>
                </span>
                    
                    <div class="relative w-full h-[232px] max-[1199px]:h-[200px] max-[767px]:h-[180px] max-[639px]:h-[160px] flex items-center justify-center overflow-hidden rounded-[4px] border border-[#D9D9D9]" data-edge-bg-target>
                        <div class="absolute inset-0 bg-[#FFF5E6] opacity-0 group-hover:opacity-30 transition-opacity duration-300 z-10"></div>
                        
                        <img src="{{ asset('storage/' . $prodRel->image) }}" 
                             alt="{{ $prodRel->title }}" 
                             data-edge-bg-image
                             class="h-full w-full object-contain transition-transform duration-300">
                    </div>

                    <div class="pt-[12px] max-[639px]:pt-3 px-[13px] max-[639px]:px-3 pb-3">
                        <p class="text-[#E40044] font-inter text-[14px] max-[639px]:text-[13px] font-bold uppercase">
                            {{ $prodRel->categoria ? $prodRel->categoria->title : '' }}
                        </p>
                        <h3 class="text-black font-inter text-[16px] max-[639px]:text-[15px] font-normal uppercase line-clamp-2">
                            {{ $prodRel->title }}
                        </h3>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    @endif

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
        
        <div @click="showModal = false" class="absolute inset-0"></div>

        <button @click="showModal = false" 
                class="absolute top-6 max-[639px]:top-4 right-6 max-[639px]:right-4 text-white cursor-pointer transition-transform p-2 z-[100] hover:scale-110">
            <svg class="w-8 h-8 max-[639px]:w-6 max-[639px]:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <div class="relative w-full h-full flex flex-col items-center justify-center p-4 max-[639px]:p-2 pointer-events-none">
            <div class="relative w-full h-[70vh] max-[639px]:h-[60vh] flex items-center justify-center pointer-events-auto">
                @foreach($allImages as $index => $image)
                    <div x-show="currentIndex === {{ $index }}" 
                         x-transition:enter="transition-opacity duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="absolute inset-0 flex items-center justify-center px-16 max-[767px]:px-12 max-[639px]:px-8"
                         style="display: none;">
                        <img src="{{ asset('storage/' . $image) }}" 
                             alt="Imagen {{ $index + 1 }}"
                             class="max-h-full max-w-full object-contain">
                    </div>
                @endforeach

                <button @click.stop="currentIndex = (currentIndex - 1 + totalImages) % totalImages" 
                        class="absolute left-4 md:left-10 max-[639px]:left-2 text-[60px] md:text-[80px] max-[639px]:text-[40px] cursor-pointer p-4 md:p-10 max-[639px]:p-2 rounded-full text-white hover:text-[#E40044] transition-all select-none leading-none z-10">
                    ‹
                </button>
                
                <button @click.stop="currentIndex = (currentIndex + 1) % totalImages" 
                        class="absolute right-4 md:right-10 max-[639px]:right-2 text-[60px] md:text-[80px] max-[639px]:text-[40px] cursor-pointer p-4 md:p-10 max-[639px]:p-2 rounded-full text-white hover:text-[#E40044] transition-all select-none leading-none z-10">
                    ›
                </button>
            </div>

            <div class="mt-8 max-[639px]:mt-4 flex gap-2 max-[639px]:gap-1 overflow-x-auto max-w-full p-2 max-[639px]:p-1 pointer-events-auto scrollbar-hide">
                @foreach($allImages as $index => $image)
                    <button @click.stop="currentIndex = {{ $index }}" 
                            :class="currentIndex === {{ $index }} ? 'border-[#E40044] scale-110' : 'border-transparent opacity-50'"
                            class="w-16 h-16 max-[639px]:w-12 max-[639px]:h-12 border-2 rounded cursor-pointer transition-all duration-300 flex-shrink-0 p-1 hover:opacity-100"
                            data-edge-bg-target>
                        <img src="{{ asset('storage/' . $image) }}" 
                             alt="Miniatura {{ $index + 1 }}"
                             data-edge-bg-image
                             class="w-full h-full object-contain">
                    </button>
                @endforeach
            </div>

            <div class="mt-4 max-[639px]:mt-3 text-white text-sm max-[639px]:text-xs pointer-events-auto">
                <span x-text="currentIndex + 1"></span> / <span x-text="totalImages"></span>
            </div>
        </div>
    </div>
</template>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@include('partials.todotex-edge-backgrounds')
</section>
