<section class="bg-white w-full mt-[94px] max-xl:mt-[60px] max-md:mt-[40px] max-[639px]:mt-[28px] max-xl:pt-[60px] max-xl:pb-[80px] max-md:pt-[20px] max-[639px]:pt-[12px] max-[639px]:pb-[56px] overflow-hidden"
    x-data="{ show: false }"
    x-intersect.once.threshold.0.1="show = true">

    <div class="max-w-[1224px] mx-auto max-xl:px-6 max-md:px-4">

        <div class="flex items-center justify-between mb-[39px] max-md:mb-[28px] max-md:flex-col max-md:items-center max-md:gap-4 max-md:text-center transition-all duration-700 ease-out transform"
             :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">

            <div class="max-md:w-full max-md:flex max-md:flex-col max-md:items-center">
                <h2 class="text-[#111010] font-inter text-[32px] font-semibold leading-[28px] max-lg:text-[30px] max-md:text-[28px] max-[639px]:text-[25px] max-[639px]:leading-[31px]">
                    Productos destacados
                </h2>
            </div>

     

        </div>

        @if($destacados->count() > 0)
        <div class="relative group/slider"
             x-show="show"
             x-transition:enter="transition ease-out duration-1000 delay-200"
             x-transition:enter-start="opacity-0 translate-y-8"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div class="swiper productosDestacadosSwiper pb-[80px]! max-md:pb-[40px] !px-1">
                <div class="swiper-wrapper">
                    @foreach($destacados as $producto)
                    @php
                        $portada = $producto->gallery->firstWhere('portada', true) ?? $producto->gallery->first();
                        $familia = $producto->categorias->first()?->familia;
                    @endphp
                    <div class="swiper-slide">
                        <a wire:navigate href="{{ route('productos') }}?productoId={{ $producto->id }}"
                           class="bg-white rounded-[4px] border border-[#EAEAEA] min-h-[400px] max-[767px]:min-h-[360px] overflow-hidden hover:shadow-md transition-shadow duration-200 flex flex-col text-left cursor-pointer w-full">

                            <div class="w-full overflow-hidden relative h-[248px] max-[767px]:h-[220px] max-[480px]:h-[200px]">
                                @if($portada)
                                    <img src="{{ Storage::url($portada->image) }}"
                                         alt="{{ $producto->codigo }}"
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

                            <div class="pt-[22px] px-[18px] pb-[15px] max-[480px]:pt-[18px] max-[480px]:px-[16px] flex flex-col gap-[16px] max-[480px]:gap-[12px] flex-1">
                                <div class="flex items-center justify-between gap-2 max-[480px]:flex-col max-[480px]:items-start max-[480px]:gap-1">
                                    @if($producto->codigo)
                                        <span class="text-[#018637] font-inter text-[16px] max-[480px]:text-[14px] font-bold leading-normal uppercase tracking-wide">
                                            COD.{{ $producto->codigo }}
                                        </span>
                                    @else
                                        <span></span>
                                    @endif
                                    @if($familia)
                                        <span class="text-[#23378C] font-inter text-[16px] max-[480px]:text-[14px] font-bold uppercase tracking-wide shrink-0 max-[480px]:shrink">
                                            {{ $familia->titulo }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[#131313] font-inter text-[21px] max-[480px]:text-[18px] font-bold leading-snug line-clamp-3">
                                    {!! strip_tags($producto->descripcion) !!}
                                </p>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
        @else
        <div class="py-12 flex flex-col items-center justify-center opacity-0 animate-[fadeIn_0.5s_ease-out_forwards]">
            <p class="text-gray-400 font-inter text-lg">No hay productos destacados disponibles.</p>
        </div>
        @endif

    </div>

    <style>
        .productosDestacadosSwiper {
            overflow: hidden;
            position: relative;
        }

        .productosDestacadosSwiper .swiper-slide {
            width: 288px;
            flex-shrink: 0;
            height: auto;
        }

        @media (max-width: 1199px) {
            .productosDestacadosSwiper .swiper-slide {
                width: 270px;
            }
        }

        @media (max-width: 767px) {
            .productosDestacadosSwiper .swiper-slide {
                width: 100% !important;
            }
        }

        .productosDestacadosSwiper .swiper-button-disabled {
            opacity: 0.35 !important;
            pointer-events: none;
            cursor: not-allowed;
        }
    </style>

    <script>
    (function() {
        let productosDestacadosSwiperInstance = null;

        function initProductosDestacadosSwiper() {
            if (productosDestacadosSwiperInstance) {
                productosDestacadosSwiperInstance.destroy(true, true);
                productosDestacadosSwiperInstance = null;
            }

            const swiperElement = document.querySelector('.productosDestacadosSwiper');
            if (!swiperElement) return;

            setTimeout(() => {
                productosDestacadosSwiperInstance = new Swiper('.productosDestacadosSwiper', {
                    slidesPerView: 'auto',
                    spaceBetween: 24,
                    speed: 600,
                    loop: true,
                    grabCursor: true,
                    watchOverflow: true,
                    autoplay: {
                        delay: 4000,
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
            document.addEventListener('DOMContentLoaded', initProductosDestacadosSwiper);
        } else {
            initProductosDestacadosSwiper();
        }

        document.addEventListener('livewire:navigated', initProductosDestacadosSwiper);

        document.addEventListener('livewire:navigating', () => {
            if (productosDestacadosSwiperInstance) {
                productosDestacadosSwiperInstance.destroy(true, true);
                productosDestacadosSwiperInstance = null;
            }
        });
    })();
    </script>
</section>
