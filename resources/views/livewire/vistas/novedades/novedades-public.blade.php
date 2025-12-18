@php
    use Carbon\Carbon;
@endphp

<div
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, 50)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 transform -translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0"
>

    @if($banner && $banner->image_banner)
    <div class="relative w-full h-[400px] max-[767px]:h-[300px] max-[639px]:h-[250px]">
        <img src="{{ Storage::url($banner->image_banner) }}" 
             alt="Novedades"
             class="w-full h-full object-cover">
    
        <div class="absolute top-[114px] left-0 right-0 z-30 max-[1199px]:top-[80px]">
            <div class="max-w-[1224px] mx-auto">
                <nav class="text-white font-montserrat text-[12px] leading-[150%] flex items-center gap-1">
                    <a wire:navigate href="{{ url('/') }}" class="text-white font-montserrat text-[12px] font-bold leading-[150%]">Inicio</a>
                    <span class="text-white font-montserrat text-[12px] leading-[150%]">›</span>
                    <span class="text-white font-montserrat text-[12px] leading-[150%]">Novedades</span>
                </nav>
            </div>
        </div>
    
        <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/50 to-transparent"></div>
    
        <div class="absolute inset-0 flex top-[216px] justify-center max-[1199px]:top-[180px] max-[991px]:top-[160px] max-[767px]:top-[140px] max-[639px]:top-[120px]">
            <h1 class="text-white text-center font-montserrat text-[48px] font-semibold leading-[120%]  max-[1199px]:text-[42px] max-[991px]:text-[38px] max-[767px]:text-[32px] max-[639px]:text-[26px]">
                Novedades
            </h1>
        </div>
    </div>
    @endif


    <div class="w-full">
        <div class="max-w-[1224px] mx-auto w-full pt-[80px] max-[1199px]:px-4 max-[1199px]:pt-[40px]">
    
            @if($destacadas->count() > 0)
                <div class="swiper novedadesDestacadasSwiper mb-[80px] max-[1199px]:mb-[40px]">
                    <div class="swiper-wrapper">
                        @foreach ($destacadas as $nov)
                        <div class="swiper-slide">
                            <a href="/novedades/{{ $nov->id }}" 
                               class="block w-full h-[532px] bg-white rounded-[12px] overflow-hidden cursor-pointer group max-[1199px]:h-auto">
    
                                <div class="w-full h-[288px] overflow-hidden max-[1199px]:h-[200px]">
                                    <img src="{{ Storage::url($nov->image) }}"
                                         alt="{{ $nov->title }}"
                                         loading="lazy"
                                         class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                                </div>
    
                                <div class="flex flex-col justify-between h-[243px] max-[1199px]:h-auto max-[1199px]:p-4">
    
                                    <div>
                                        <p class="text-black font-montserrat text-[16px] font-normal leading-[120%] mt-[16px] mb-[10px] max-[1199px]:text-[14px] max-[1199px]:mt-0">
                                            {{ $nov->novcategories->first()->title ?? 'Novedad' }}
                                        </p>
    
                                        <h3 class="text-black font-montserrat text-[24px] font-medium leading-[120%] mb-[16px] line-clamp-2 max-[1199px]:text-[20px] max-[1199px]:mb-[12px]">
                                            {{ $nov->title }}
                                        </h3>
    
                                        <p class="text-black font-montserrat text-[16px] font-normal leading-[150%] line-clamp-3 max-[1199px]:text-[14px]">
                                            {{ preg_replace('/(&nbsp;)+$/', '', strip_tags($nov->description))}}
                                        </p>
                                    </div>
                                    
                                    <span class="text-[#323232] font-montserrat text-[16px] opacity-50 font-normal leading-[150%] hover:underline max-[1199px]:text-[14px] max-[1199px]:mt-3">
                                        Ver más
                                    </span>
    
                                </div>
    
                            </a>
                        </div>
                        @endforeach
                    </div>
    
                    <div class="swiper-button-next novedades-destacadas-next"></div>
                    <div class="swiper-button-prev novedades-destacadas-prev"></div>
                </div>
            @endif
    
        </div>
    </div>
    
    <style>
        .novedadesDestacadasSwiper {
            overflow: hidden;
            position: relative;
        }
    
        .novedadesDestacadasSwiper .swiper-slide {
            width: 392px;
            flex-shrink: 0;
        }
    
        @media (max-width: 1199px) {
            .novedadesDestacadasSwiper .swiper-slide {
                width: 100%;
            }
        }
    
        .novedadesDestacadasSwiper .swiper-button-next,
        .novedadesDestacadasSwiper .swiper-button-prev {
            color: rgba(255,255,255,1);
            width: 60px;
            height: 60px;
            opacity: 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
            transition: all 0.3s ease;
            z-index: 10;
        }
    
        .novedadesDestacadasSwiper:hover .swiper-button-next,
        .novedadesDestacadasSwiper:hover .swiper-button-prev {
            opacity: 1;
        }
    
        .novedadesDestacadasSwiper .swiper-button-next:hover,
        .novedadesDestacadasSwiper .swiper-button-prev:hover {
            transform: scale(1.1);
        }
    
        .novedadesDestacadasSwiper .swiper-button-disabled {
            opacity: 0.35 !important;
            pointer-events: none;
        }

        @media (max-width: 1199px) {
            .novedadesDestacadasSwiper .swiper-button-next,
            .novedadesDestacadasSwiper .swiper-button-prev {
                opacity: 0.8;
                width: 40px;
                height: 40px;
            }

            .novedadesDestacadasSwiper .swiper-button-next::after,
            .novedadesDestacadasSwiper .swiper-button-prev::after {
                font-size: 20px;
            }
        }
    </style>
    
    <script>
    (function() {
        let novedadesDestacadasSwiperInstance = null;
    
        function initNovedadesDestacadasSwiper() {
            if (novedadesDestacadasSwiperInstance) {
                novedadesDestacadasSwiperInstance.destroy(true, true);
                novedadesDestacadasSwiperInstance = null;
            }
    
            const swiperElement = document.querySelector('.novedadesDestacadasSwiper');
            if (!swiperElement) return;
    
            setTimeout(() => {
                const isMobile = window.innerWidth < 1200;
                
                novedadesDestacadasSwiperInstance = new Swiper('.novedadesDestacadasSwiper', {
                    slidesPerView: isMobile ? 1 : 'auto',
                    spaceBetween: 24,
                    speed: 400,
                    loop: true,
                    grabCursor: true,
                    slideToClickedSlide: false,
                    watchOverflow: true,
                    autoplay: {
                        delay: 3500,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    navigation: {
                        nextEl: '.novedades-destacadas-next',
                        prevEl: '.novedades-destacadas-prev',
                    },
                    breakpoints: {
                        0: {
                            slidesPerView: 1,
                            spaceBetween: 16,
                        },
                        1200: {
                            slidesPerView: 'auto',
                            spaceBetween: 24,
                        },
                    }
                });
            }, 150);
        }
    
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initNovedadesDestacadasSwiper);
        } else {
            initNovedadesDestacadasSwiper();
        }
    
        document.addEventListener('livewire:navigated', initNovedadesDestacadasSwiper);
        
        document.addEventListener('livewire:navigating', () => {
            if (novedadesDestacadasSwiperInstance) {
                novedadesDestacadasSwiperInstance.destroy(true, true);
                novedadesDestacadasSwiperInstance = null;
            }
        });
    })();
    </script>


    <div class="w-full bg-white pb-[93px] max-[1199px]:pb-[60px]">
    <div class="max-w-[1224px] mx-auto w-full max-[1199px]:px-4">

        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,3fr)_minmax(260px,1fr)] gap-[32px]
                    max-[1199px]:flex max-[1199px]:flex-col max-[1199px]:gap-[24px]">

            <div class="space-y-[24px] w-[810px] max-[1199px]:order-2 max-[1199px]:w-full">

                @if($novedades->count() === 0)
                    <p class="text-gray-500">No se encontraron novedades.</p>
                @else

                <div class="grid grid-cols-1 md:grid-cols-2 gap-[24px] justify-items-start max-[1199px]:grid-cols-1 max-[1199px]:gap-[16px]">
                    @foreach ($novedades as $nov)
                    <a wire:navigate href="/novedades/{{ $nov->id }}" 
                        class="w-full md:w-[392px] h-[532px] bg-white rounded-[12px] overflow-hidden cursor-pointer group
                               max-[1199px]:h-auto max-[1199px]:w-full">

                       <div class="w-full h-[288px] overflow-hidden max-[1199px]:h-[200px]">
                           <img src="{{ Storage::url($nov->image) }}"
                                class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                       </div>

                       <div class="flex flex-col justify-between
                                   h-[243px] max-[1199px]:h-auto max-[1199px]:p-4">

                           <div>
                               <p class="text-black font-montserrat text-[16px] font-normal leading-[120%] mt-[16px] mb-[10px] max-[1199px]:text-[14px] max-[1199px]:mt-0">
                                   {{ $nov->novcategories->first()->title ?? 'Novedad' }}
                               </p>

                               <h3 class="text-black font-montserrat text-[24px] font-medium leading-[120%] mb-[16px] max-[1199px]:text-[20px] max-[1199px]:mb-[12px]">
                                   {{ $nov->title }}
                               </h3>

                               <p class="text-black font-montserrat text-[16px] font-normal leading-[150%] line-clamp-3 max-[1199px]:text-[14px]">
                                   {{ preg_replace('/(&nbsp;)+$/', '', strip_tags($nov->description))}}
                               </p>
                           </div>
                           <span class="text-[#323232] font-montserrat text-[16px] opacity-50 font-normal leading-[150%] hover:underline max-[1199px]:text-[14px] max-[1199px]:mt-3">
                               Ver más
                           </span>


                       </div>

                   </a>
                    @endforeach
                </div>

                    @if($novedades->total() > $novedades->perPage())
                    <div class="mt-10 flex flex-col items-center gap-3 max-[1199px]:mt-6">

                        <div class="flex justify-center">
                            {{ $novedades->links() }}
                        </div>

                        <p class="text-sm text-gray-600 max-[1199px]:text-xs">
                            Mostrando 
                            {{ $novedades->firstItem() }} 
                            a 
                            {{ $novedades->lastItem() }} 
                            de 
                            {{ $novedades->total() }} 
                            resultados
                        </p>

                    </div>
                    @endif

                @endif

            </div>

            <div class="max-[1199px]:order-1 max-[1199px]:mb-0">

                <div class="relative max-[1199px]:w-full">
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        placeholder="Buscar..."
                        class="rounded-[26px] border border-[#DEDFE0] w-[288px] h-[54px] placeholder:text-black pl-[17px] font-montserrat text-[14px] font-normal leading-[150%] max-[1199px]:w-full"
                    >
                    <svg class="w-4 h-4 absolute right-6 top-1/2 -translate-y-1/2 text-[#222]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                    </svg>
                </div>

                <div x-data="{ openCat: true }" class="pt-[42px] max-[1199px]:pt-[24px]">
                    <div class="flex items-center justify-between cursor-pointer mb-[6px] w-[288px]
                                max-[1199px]:w-full"
                         @click="openCat = !openCat">

                        <h3 class="text-black font-montserrat text-[20px] font-normal leading-[120%] max-[1199px]:text-[18px]">Categorías</h3>

                        <svg class="w-4 h-4 text-[#1B1A17] transition-transform duration-300"
                             :class="openCat ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    <div class="space-y-1 border-t border-[#EEEEEF] pt-3 w-[288px]
                                max-[1199px]:w-full"
                         x-show="openCat"
                         x-transition>

                        <button
                            type="button"
                            wire:click="clearCategory"
                            class="w-[288px] h-[50px] flex items-center justify-between pl-[17px] rounded-[10px]
                                   max-[1199px]:w-full cursor-pointer
                                   {{ $category === null 
                                        ? 'bg-[#BA2025] text-white' 
                                        : 'bg-[#EEEEEF] text-[#1B1A17] hover:bg-[#BA2025] hover:text-white' }}">
                            <span>Todas</span>
                        </button>

                        @foreach($categories as $cat)
                        <button
                            type="button"
                            wire:click="$set('category', {{ $cat->id }})"
                            class="w-[288px] h-[50px] flex pl-[17px] items-center justify-between rounded-[10px]
                                   max-[1199px]:w-full cursor-pointer
                                   {{ $category === $cat->id 
                                        ? 'bg-[#BA2025] text-white' 
                                        : 'bg-[#F5F5F5] text-[#1B1A17] hover:bg-[#BA2025] hover:text-white' }}">
                            <span>{{ $cat->title }}</span>
                            <span class="w-[50px] h-[50px] bg-[#313131] flex-shrink-0 text-white text-center font-jost text-base font-medium leading-[50px] rounded-tr-[10px] rounded-br-[10px]">
                                {{ $cat->novedades_count }}
                            </span>
                        </button>
                        @endforeach

                    </div>

                </div>


                <div x-data="{ openArc: true }" class="pt-[30px] w-[288px] max-[1199px]:w-full max-[1199px]:pt-[24px]">

                    <div class="flex items-center justify-between cursor-pointer mb-3"
                         @click="openArc = !openArc">

                        <h3 class="text-black font-montserrat text-[20px] font-normal leading-[120%] max-[1199px]:text-[18px]">
                            Archivo
                        </h3>

                        <svg class="w-4 h-4 text-[#1B1A17] transition-transform duration-300"
                             :class="openArc ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

                    <div class="space-y-1 border-t border-[#EEEEEF] pt-3"
                         x-show="openArc"
                         x-transition>

                        <button
                            type="button"
                            wire:click="clearMonth"
                            class="w-[288px] h-[50px] pl-[17px] flex items-center justify-between rounded-[10px]
                                   max-[1199px]:w-full cursor-pointer
                                   {{ $month === null 
                                        ? 'bg-[#BA2025] text-white' 
                                        : 'bg-[#EEEEEF] text-[#222] hover:bg-[#BA2025] hover:text-white' }}">
                            <span class="font-montserrat text-[16px] font-normal leading-[150%]">Todos</span>
                        </button>

                        @foreach($archive as $item)
                        @php
                            $date = Carbon::create($item->year, $item->month, 1);
                            $value = $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                        @endphp

                        <button
                            type="button"
                            wire:click="$set('month', '{{ $value }}')"
                            class="w-[288px] h-[50px] pl-[17px] flex items-center justify-between
                                   max-[1199px]:w-full cursor-pointer rounded-[10px]
                                   {{ $month === $value 
                                        ? 'bg-[#BA2025] text-white' 
                                        : 'bg-[#F5F5F5] text-[#1B1A17] hover:bg-[#BA2025] hover:text-white' }}">
                            <span>{{ $date->translatedFormat('F Y') }}</span>
                            <span class="w-[50px] h-[50px] bg-[#313131] text-white text-center font-jost text-base font-medium leading-[50px] rounded-tr-[10px] rounded-br-[10px]">
                                {{ $item->count }}
                            </span>
                        </button>
                        @endforeach

                    </div>

                </div>

            </div>

        </div>
    </div>

</div>


</div>