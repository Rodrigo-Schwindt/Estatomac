<section class="bg-white w-full mt-[94px] max-[1199px]:pt-[60px] max-[1199px]:pb-[80px] max-[767px]:pt-[0px] max-[767px]:pb-[60px]">
    <div class="max-w-[1224px] mx-auto max-[1199px]:px-6 max-[767px]:px-4">

        <div class="flex items-center justify-between mb-[39px] max-[767px]:flex-col max-[767px]:items-center max-[767px]:gap-4 max-[767px]:text-center">
            <div class="max-[767px]:w-full max-[767px]:flex max-[767px]:flex-col max-[767px]:items-center">
                <h2 class="text-[#111010] font-inter text-[32px] font-semibold leading-[28px] max-[991px]:text-[36px] max-[767px]:text-[32px] max-[639px]:text-[28px]">
                    Lanzamientos
                </h2>
            </div>

            <a wire:navigate href="{{ url('/productos') }}"
               class="transition-all duration-300 hover:bg-[#E4002B] hover:text-white flex justify-center items-center rounded-[4px] border border-[#E40044] w-[116px] h-[44px] text-[#E40044] text-center font-inter text-[14px] font-normal leading-normal uppercase bg-transparent">
                Ver todas
            </a>
        </div>

        @if($destacados->count() > 0)
        <div class="swiper productosDestacadosSwiper">
            <div class="swiper-wrapper">
                @foreach($destacados as $producto)
                <div class="swiper-slide">
                    <a  href="/productos/{{ $producto->id }}" class="group relative bg-white overflow-hidden rounded-[4px] h-[312px] block">
    
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
                </div>
                @endforeach
            </div>

        </div>
        @else
        <p class="text-gray-500 text-center py-8">No hay productos destacados disponibles.</p>
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
        }

        @media (max-width: 991px) {
            .productosDestacadosSwiper .swiper-slide {
                width: 260px;
            }
        }

        @media (max-width: 767px) {
            .productosDestacadosSwiper .swiper-slide {
                width: 240px;
            }
        }

        @media (max-width: 639px) {
            .productosDestacadosSwiper .swiper-slide {
                width: 220px;
            }
        }

        .productosDestacadosSwiper .swiper-button-next,
        .productosDestacadosSwiper .swiper-button-prev {
            color: rgba(255,255,255,1);
            width: 60px;
            height: 60px;
            opacity: 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
            transition: all 0.3s ease;
            z-index: 10;
        }

        @media (max-width: 767px) {
            .productosDestacadosSwiper .swiper-button-next,
            .productosDestacadosSwiper .swiper-button-prev {
                width: 50px;
                height: 50px;
            }
        }

        @media (max-width: 639px) {
            .productosDestacadosSwiper .swiper-button-next,
            .productosDestacadosSwiper .swiper-button-prev {
                width: 40px;
                height: 40px;
            }
        }

        .productosDestacadosSwiper:hover .swiper-button-next,
        .productosDestacadosSwiper:hover .swiper-button-prev {
            opacity: 1;
        }

        .productosDestacadosSwiper .swiper-button-next:hover,
        .productosDestacadosSwiper .swiper-button-prev:hover {
            transform: scale(1.1);
        }

        .productosDestacadosSwiper .swiper-button-disabled {
            opacity: 0.35 !important;
            pointer-events: none;
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
                        nextEl: '.productos-destacados-next',
                        prevEl: '.productos-destacados-prev',
                    },
                    breakpoints: {
                        0: {
                            spaceBetween: 12,
                            slidesPerView: 1,
                        },
                        640: {
                            spaceBetween: 16,
                        },
                        768: {
                            spaceBetween: 20,
                        },
                        1024: {
                            spaceBetween: 24,
                        },
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