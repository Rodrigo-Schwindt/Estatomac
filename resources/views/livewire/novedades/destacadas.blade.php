<section class="bg-white w-full pt-[90px] pb-[72px] max-[1199px]:pt-[60px] max-[1199px]:pb-[80px] max-[767px]:pt-[0px] max-[767px]:pb-[60px]">
    <div class="max-w-[1224px] mx-auto max-[1199px]:px-6 max-[767px]:px-4">

        <div class="flex items-center justify-between mb-[24px] max-[767px]:flex-col max-[767px]:items-center max-[767px]:gap-4 max-[767px]:text-center">
            <div class="max-[767px]:w-full max-[767px]:flex max-[767px]:flex-col max-[767px]:items-center">
                <h2 class="text-[#111010] font-inter text-[32px] font-semibold leading-[28px] mb-[22px] max-[991px]:text-[36px] max-[767px]:text-[32px] max-[639px]:text-[28px]">
                    Novedades
                </h2>
            </div>
        </div>

        @if($destacadas->count() > 0)
        <div class="swiper novedadesDestacadasSwiper">
            <div class="swiper-wrapper">
                @foreach($destacadas as $nov)
                <div class="swiper-slide">
                    <a href="/novedades/{{ $nov->id }}"
                       class="block rounded-[4px] border border-[#DDDDE0] w-full bg-white rounded-[4px] overflow-hidden cursor-pointer group max-[991px]:h-[500px] max-[767px]:h-[480px] max-[639px]:h-auto">

                        <div class="w-full h-[263px] overflow-hidden max-[991px]:h-[260px] max-[767px]:h-[240px] max-[639px]:h-[220px]">
                            <img src="{{ Storage::url($nov->image) }}"
                                 alt="{{ $nov->title }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                        </div>

                        <div class="flex flex-col  px-[16px] pb-[12px] justify-between h-[243px] max-[991px]:h-[240px] max-[767px]:h-[240px] max-[639px]:h-auto max-[639px]:p-4">
                            <div>
                                <p class="text-[#E40044] font-inter text-[14px] font-bold leading-[22px] mb-[10px] mt-[18px] max-[767px]:text-[15px] max-[639px]:text-[14px] max-[639px]:mt-0">
                                    {{ $nov->novcategories->first()->title ?? 'Novedad' }}
                                </p>

                                <h3 class="text-black font-montserrat text-[24px] font-medium leading-[120%] mb-[6px] line-clamp-2 max-[991px]:text-[22px] max-[767px]:text-[20px] max-[639px]:text-[18px] max-[639px]:mb-3">
                                    {{ $nov->title }}
                                </h3>

                                <p class=" text-black  font-inter text-[16px] font-normal leading-[25px] line-clamp-3 max-[767px]:text-[15px] max-[639px]:text-[14px]">
                                    {{ preg_replace('/(&nbsp;)+$/', '', strip_tags($nov->description)) }}
                                </p>
                            </div>
                            
                            <span class="text-black font-inter text-[16px] font-bold leading-normal hover:underline max-[767px]:text-[15px] max-[639px]:text-[14px] max-[639px]:mt-3">
                                Leer más
                            </span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

        </div>
        @else
        <p class="text-gray-500 text-center py-8">No hay novedades destacadas disponibles.</p>
        @endif

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

        @media (max-width: 991px) {
            .novedadesDestacadasSwiper .swiper-slide {
                width: 340px;
            }
        }

        @media (max-width: 767px) {
            .novedadesDestacadasSwiper .swiper-slide {
                width: 300px;
            }
        }

        @media (max-width: 639px) {
            .novedadesDestacadasSwiper .swiper-slide {
                width: 280px;
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

        @media (max-width: 767px) {
            .novedadesDestacadasSwiper .swiper-button-next,
            .novedadesDestacadasSwiper .swiper-button-prev {
                width: 50px;
                height: 50px;
            }
        }

        @media (max-width: 639px) {
            .novedadesDestacadasSwiper .swiper-button-next,
            .novedadesDestacadasSwiper .swiper-button-prev {
                width: 40px;
                height: 40px;
            }
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
                novedadesDestacadasSwiperInstance = new Swiper('.novedadesDestacadasSwiper', {
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
                        nextEl: '.novedades-destacadas-next',
                        prevEl: '.novedades-destacadas-prev',
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
</section>