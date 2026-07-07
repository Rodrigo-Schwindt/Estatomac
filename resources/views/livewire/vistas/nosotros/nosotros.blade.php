<div x-data="{ show: false, shownSection2: false }" x-init="setTimeout(() => show = true, 50)" x-show="show" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="w-full overflow-hidden">

    <div class="bg-[#F5F5F5]  h-[150px]">
        <div class="max-w-[1224px] mx-auto">
                <div class=" max-[1199px]:px-4 ">
            <div class="max-w-[1224px] mx-auto pt-[16px]">
                <nav class="text-white font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] flex items-center gap-1">
                    <a wire:navigate href="{{ url('/') }}" class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] font-bold leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Inicio</a>
                    <span class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">|</span>
                    <span class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Nosotros</span>
                </nav>
            </div>
        </div>
        <h1 class="text-[40px] pt-[43px] font-bold  max-[900px]:px-4">Nosotros</h1>
        </div>
    </div>
    
    <section class="relative mt-[40px] md:mt-[60px] min-[1200px]:mt-[73px] mb-[60px] min-[1200px]:mb-[80px] px-4 min-[1200px]:px-0">
        <div class="max-w-[1224px] h-auto min-[1200px]:h-[422px] mx-auto grid grid-cols-1 min-[1200px]:grid-cols-2 gap-[32px] md:gap-[40px] min-[1200px]:gap-[55px] items-start">
    
            
            <div class="flex flex-col w-full  animate-slide-in-left">
                <h1 class="text-[#111010] font-inter text-[24px] md:text-[32px] min-[1200px]:text-[32px] font-semibold leading-normal tracking-[-0.01em] mt-[16px] md:mt-[42px] min-[1200px]:mt-[28px] mb-[16px] min-[1200px]:mb-[16px]">
                    {{ $nosotros->title }}
                </h1>
                
                <div class="[&>*]:text-black [&>*]:font-inter [&>*]:text-[15px] md:[&>*]:text-[16px] [&>*]:font-normal [&>*]:leading-[160%] [&>*]:m-0 [&>*]:mb-4 opacity-90">
                    {!! str_replace('&nbsp;', ' ', $nosotros->description) !!}
                </div>
            </div>
            <div class="w-full h-[250px] md:h-[424px] min-[1200px]:h-[424px] animate-scale-in group overflow-hidden rounded-[6px]">
                <img src="{{ Storage::url($nosotros->image) }}"
                     alt="{{ $nosotros->title }}"
                     class="w-full h-full object-cover rounded-[6px] transition-transform duration-700 group-hover:scale-105">
            </div>
    
        </div>
    </section>
    
    <section class="px-4 min-[1200px]:px-0 bg-[#F5F5F5] pt-[50px] min-[1200px]:pt-[60px] pb-[75px]"
             x-intersect.once="shownSection2 = true">
    
        <h2 class="max-w-[1224px] mx-auto text-[#111010] font-inter text-[26px] md:text-[28px] min-[1200px]:text-[32px] font-semibold leading-normal pt-[10px] min-[1200px]:pt-[50px] pb-[30px] min-[1200px]:pb-[25px] text-center min-[1200px]:text-left transition-opacity duration-700"
            :class="shownSection2 ? 'opacity-100' : 'opacity-0'">
            ¿Por qué elegirnos?
        </h2>
    
        <div class="max-w-[1224px] mx-auto grid grid-cols-1 md:grid-cols-3 gap-[24px] min-h-[288px]">
    
            <div class="px-[25px] pt-[61px] min-h-[392px] flex flex-col bg-white rounded-[6px]   animate-card-1   transition-all duration-300">
                <div class='w-[60px] h-[60px] rounded-[100px] bg-white flex items-center justify-center'>
                    @if($nosotros->image_1)
                        <img src="{{ asset('storage/' . $nosotros->image_1) }}"
                             class="h-[full] w-[full]">
                    @endif
                </div>
                <h3 class="text-[#23378C]  font-inter text-[20px] font-bold leading-[120%] mb-[15px] mt-[33px]">
                    {{ $nosotros->title_1 }}
                </h3>
    
                <div class="[&>*]:text-black  [&>*]:font-inter [&>*]:text-[16px] [&>*]:font-normal [&>*]:leading-[150%]">
                    {!! str_replace('&nbsp;', ' ', $nosotros->description_1) !!}
                </div>
            </div>
    
            <div class="px-[25px] pt-[61px] min-h-[392px] min-w-[392px] flex flex-col  bg-white rounded-[6px]  animate-card-2   transition-all duration-300">
                <div class='w-[60px] h-[60px] rounded-[100px] bg-white flex items-center justify-center'>
                    @if($nosotros->image_2)
                        <img src="{{ asset('storage/' . $nosotros->image_2) }}"
                             class="h-[full] w-[full]">
                    @endif
                </div>
    
                <h3 class="text-[#23378C]  font-inter text-[20px] font-bold leading-[120%] mb-[15px] mt-[33px]">
                    {{ $nosotros->title_2 }}
                </h3>
    
                <div class="[&>*]:text-black  [&>*]:font-inter [&>*]:text-[16px] [&>*]:font-normal [&>*]:leading-[150%]">
                    {!! str_replace('&nbsp;', ' ', $nosotros->description_2) !!}
                </div>
            </div>
    
            <div class="px-[25px] pt-[61px] min-h-[392px] min-w-[392px] flex flex-col  rounded-[6px] bg-white animate-card-3  transition-all duration-300">
                <div class='w-[60px] h-[60px] rounded-[100px] bg-white flex items-center justify-center'>
                    @if($nosotros->image_3)
                        <img src="{{ asset('storage/' . $nosotros->image_3) }}"
                             class="h-[full] w-[full]">
                    @endif
                </div>
    
                <h3 class="text-[#23378C]  font-inter text-[20px] font-bold leading-[120%] mb-[15px] mt-[33px]">
                    {{ $nosotros->title_3 }}
                </h3>
    
                <div class="[&>*]:text-black  [&>*]:font-inter [&>*]:text-[16px] [&>*]:font-normal [&>*]:leading-[150%]">
                    {!! str_replace('&nbsp;', ' ', $nosotros->description_3) !!}
                </div>
            </div>
    
        </div>
    </section>
    <style> @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-30px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    
    .animate-slide-in-left { animation: slideInLeft 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }
    .animate-slide-in-right { animation: slideInRight 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }
    .animate-scale-in { animation: scaleIn 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }
    
    .animate-card-1 { animation: fadeInUp 0.7s ease-out 0.1s forwards; opacity: 0; }
    .animate-card-2 { animation: fadeInUp 0.7s ease-out 0.2s forwards; opacity: 0; }
    .animate-card-3 { animation: fadeInUp 0.7s ease-out 0.3s forwards; opacity: 0; }
    </style>
    
    </div>