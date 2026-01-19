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
                    <a wire:navigate href="{{ url('/') }}" class="text-white font-montserrat text-[12px] font-bold leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Inicio</a>
                    <span class="text-white font-montserrat text-[12px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">›</span>
                    <span class="text-white font-montserrat text-[12px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Nosotros</span>
                </nav>
            </div>
        </div>

        <div class="absolute inset-0 bg-[rgba(170,65,65,0.5)]"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-black/20 to-black/20"></div>


        <div class="absolute inset-0 flex top-[245px] max-w-[1224px] mx-auto">
            <h1 class="text-white font-inter text-[40px] font-bold leading-normal
                       max-[767px]:text-[36px] max-[639px]:text-[28px] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">
                {{ $banner->title }}
            </h1>
        </div>
    </section>
    @endif

    <section class="relative mt-[73px] mb-[80px] max-[639px]:mt-[32px] max-[1199px]:px-4 animate-fade-in-up">
        <div class="max-w-[1224px] h-[450px]  mx-auto grid grid-cols-1 md:grid-cols-2 gap-[55px] items-start">
    
            <div class="w-full h-[450px] animate-scale-in">
                <img src="{{ Storage::url($nosotros->image) }}"
                     alt="{{ $nosotros->title }}"
                     class="w-full h-full object-cover rounded-[6px]">
            </div>
            <div class="flex flex-col w-[538px] animate-slide-in-left">
                <h1 class="text-[#111010] font-inter text-[32px] font-semibold leading-normal tracking-[-0.01em] mt-[28px] mb-[40px]
                           max-[767px]:text-[26px] max-[639px]:text-[22px]">
                    {{ $nosotros->title }}
                </h1>
    
                <div class="[&>*]:text-black [&>*]:font-inter [&>*]:text-[16px] [&>*]:font-normal [&>*]:leading-[22px] [&>*]:m-0">
                    {!! str_replace('&nbsp;', ' ', $nosotros->description) !!}
                </div>
            </div>
    
    
        </div>
    </section>

    <section class="max-[1199px]:px-4 bg-[#F8F8F8] max-[1199px]:pt-[60px] pb-[75px]">
    
        <h2 class="max-w-[1224px] mx-auto text-[#111010] font-inter text-[32px] font-semibold leading-normal pt-[50px] pb-[25px]
                   max-[767px]:text-[26px] max-[639px]:text-[22px] animate-fade-in">
            ¿Por qué elegirnos?
        </h2>
    
        <div class="max-w-[1224px] mx-auto grid grid-cols-1 md:grid-cols-3 gap-[24px] min-h-[288px]">
    
            <div class="px-[25px] pt-[61px] min-h-[392px] flex flex-col bg-white rounded-[20px] items-center   animate-card-1   transition-all duration-300">
                <div class='w-[60px] h-[60px] rounded-[100px] bg-white flex items-center justify-center'>
                    @if($nosotros->image_1)
                        <img src="{{ asset('storage/' . $nosotros->image_1) }}"
                             class="h-[full] w-[full]">
                    @endif
                </div>
                <h3 class="text-[#1D1D1B] text-center font-inter text-[20px] font-bold leading-[120%] mb-[11px] mt-[32px]">
                    {{ $nosotros->title_1 }}
                </h3>
    
                <div class="[&>*]:text-black [&>*]:text-center [&>*]:font-inter [&>*]:text-[16px] [&>*]:font-normal [&>*]:leading-[150%]">
                    {!! str_replace('&nbsp;', ' ', $nosotros->description_1) !!}
                </div>
            </div>
    
            <div class="px-[25px] pt-[61px] min-w-[392px] flex flex-col items-center bg-white rounded-[20px]  animate-card-2   transition-all duration-300">
                <div class='w-[60px] h-[60px] rounded-[100px] bg-white flex items-center justify-center'>
                    @if($nosotros->image_2)
                        <img src="{{ asset('storage/' . $nosotros->image_2) }}"
                             class="h-[full] w-[full]">
                    @endif
                </div>
    
                <h3 class="text-[#1D1D1B] text-center font-inter text-[20px] font-bold leading-[120%] mb-[11px] mt-[32px]">
                    {{ $nosotros->title_2 }}
                </h3>
    
                <div class="[&>*]:text-black [&>*]:text-center [&>*]:font-inter [&>*]:text-[16px] [&>*]:font-normal [&>*]:leading-[150%]">
                    {!! str_replace('&nbsp;', ' ', $nosotros->description_2) !!}
                </div>
            </div>
    
            <div class="px-[25px] pt-[61px] min-w-[392px] flex flex-col items-center rounded-[20px] bg-white animate-card-3  transition-all duration-300">
                <div class='w-[60px] h-[60px] rounded-[100px] bg-white flex items-center justify-center'>
                    @if($nosotros->image_3)
                        <img src="{{ asset('storage/' . $nosotros->image_3) }}"
                             class="h-[full] w-[full]">
                    @endif
                </div>
    
                <h3 class="text-[#1D1D1B] text-center font-inter text-[20px] font-bold leading-[120%] mb-[11px] mt-[32px]">
                    {{ $nosotros->title_3 }}
                </h3>
    
                <div class="[&>*]:text-black [&>*]:text-center [&>*]:font-inter [&>*]:text-[16px] [&>*]:font-normal [&>*]:leading-[150%]">
                    {!! str_replace('&nbsp;', ' ', $nosotros->description_3) !!}
                </div>
            </div>
    
        </div>
    </section>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.8s ease-out forwards;
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
    }

    .animate-slide-in-left {
        animation: slideInLeft 0.9s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }

    .animate-slide-in-right {
        animation: slideInRight 0.9s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }

    .animate-scale-in {
        animation: scaleIn 0.9s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }

    .animate-card-1 {
        animation: fadeInUp 0.7s ease-out 0.1s forwards;
        opacity: 0;
    }

    .animate-card-2 {
        animation: fadeInUp 0.7s ease-out 0.2s forwards;
        opacity: 0;
    }

    .animate-card-3 {
        animation: fadeInUp 0.7s ease-out 0.3s forwards;
        opacity: 0;
    }

    .animate-card-4 {
        animation: fadeInUp 0.7s ease-out 0.4s forwards;
        opacity: 0;
    }

    .animate-slide-in-left-delayed {
        animation: slideInLeft 0.9s ease-out 0.2s forwards;
        opacity: 0;
    }

    .animate-slide-in-right-delayed {
        animation: slideInRight 0.9s ease-out 0.4s forwards;
        opacity: 0;
    }
</style>
</div>
