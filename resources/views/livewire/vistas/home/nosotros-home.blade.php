<section class="bg-[#23378C]   overflow-hidden" x-data="{ shown: false }" x-intersect.once.threshold.0.2="shown = true" > @php $nosotros = \App\Models\Nosotros::first(); @endphp

    @if($nosotros)
    <div class="flex flex-col min-[1200px]:flex-row w-full min-[1200px]:h-[600px] h-auto gap-0 min-[1200px]:gap-0 ">
        
        <div 
            class="
                w-full min-[1200px]:w-1/2 
                h-[600px] 
                max-[1199px]:h-[420px]
                max-[767px]:h-[320px]
                max-[480px]:h-[260px]
                 
                transition-all duration-1000 ease-out transform
            "
            :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-10'"
        >
            @if($nosotros?->image_home)
            <img 
                src="{{ Storage::url($nosotros->image_home) }}"
                alt="{{ $nosotros->title_home }}"
                class="
                    w-full h-full object-cover
                "
            >
            @endif
        </div>
    
        <div 
            class="
                w-full min-[1200px]:w-1/2 
                text-[#111] flex flex-col
                transition-all duration-1000 delay-300 ease-out transform
            "
            :class="shown ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-10'"
        >
            <div class="
                flex flex-col justify-between h-full 
                min-[1200px]:mt-[80px] min-[1200px]:ml-[94px]
                max-[1199px]:py-[40px] max-[1199px]:px-[32px] 
                max-md:py-[30px] max-md:px-[20px]
            ">
                
                <div class="min-[1200px]:w-[539px] w-full">
                    <h2 class="
                        text-white font-inter text-[32px] font-semibold leading-normal tracking-[-0.01em]
                        max-md:text-[28px] max-[480px]:text-[25px] max-md:text-center min-[1200px]:text-left
                    ">
                        {{ $nosotros->title_home }}
                    </h2>
    
                    <div class="
                        content-description richedit-reset 
                        text-white font-inter text-[16px] font-normal leading-[22px] tracking-tight
                        min-[1200px]:w-[542px] w-full
                        mt-[46px] max-[1199px]:mt-[28px] max-md:mt-[22px] max-md:text-center max-md:text-[15px]
                    ">
                        {!! str_replace('&nbsp;', ' ', $nosotros->description_home) !!}
                    </div>
                </div>
    
                <div class="
                    mt-auto 
                    max-[1199px]:mt-[30px] 
                    max-md:mt-[24px] 
                    flex min-[1200px]:justify-start justify-center
                ">
                    <a 
                        wire:navigate 
                        href="/nosotros"
                        class="
                            cursor-pointer flex justify-center items-center 
                            text-[#23378C] text-center font-inter text-[16px] font-normal leading-normal  
                            bg-white w-[122px] h-[40px] rounded-[40px] border border-white 
                            transition-all duration-300
                            hover:border-white hover:text-[#23378C] hover:bg-[#EEF2FF] hover:shadow-[0_8px_20px_rgba(0,0,0,0.18)]
                            active:scale-95 mb-[84px] max-[1199px]:mb-0
                        "
                    >
                        Más info
                    </a>
                </div>
    
            </div>
        </div>
    
    </div>
    @endif
    
    <style>
        .richedit-reset * {
            all: unset;
            font-family: inherit;
            font-size: inherit;
            color: inherit;
            line-height: inherit;
            display: revert;
        }
    
        .richedit-reset p {
            margin-bottom: 1rem;
            display: block;
        }
    </style>
    </section>
