<section class="bg-white mt-[80px] mb-[51px]">
    @php
        $nosotros = \App\Models\Nosotros::first();
    @endphp

    @if($nosotros)
    <div class="flex flex-col lg:flex-row w-full h-[459px] max-[1199px]:h-auto max-[1199px]:gap-8">
        
        <div class="w-full lg:w-1/2 h-[459px] py-[30px] max-lg:h-[400px] max-md:h-[300px] max-[767px]:h-[220px] flex-shrink-0 pr-[12px] max-[1199px]:pr-0 bg-[#111]">
            @if($nosotros?->image_home)
            <img 
                src="{{ Storage::url($nosotros->image_home) }}"
                alt="{{ $nosotros->title_home }}"
                class="w-full h-full object-contain object-right pr-[136px]"
            >
            @endif
        </div>

        <div class="w-full lg:w-1/2 text-[#111] flex flex-col">
            <div class="flex flex-col justify-between h-full max-lg:py-6 max-md:py-4 max-lg:px-8 max-md:px-4 lg:mt-[17px] lg:ml-[66px] max-[1199px]:mt-4">
                
                <div class="w-[539px] max-[1199px]:w-full">
                    <h2 class="text-black font-inter text-[32px] font-semibold leading-normal tracking-[-0.01em]">
                        {{ $nosotros->title_home }}
                    </h2>

                    <div class="content-description richedit-reset text-black font-inter text-[16px] font-normal leading-[22px] w-[542px] tracking-tight">
                        {!! str_replace('&nbsp;', ' ', $nosotros->description_home) !!}
                    </div>
                </div>

                <div class="mt-auto max-md:mt-4">
                    <a 
                        wire:navigate 
                        href="/nosotros"
                        class="flex justify-center items-center text-black text-center font-inter text-[14px] font-normal leading-normal uppercase bg-transparent w-[164px] h-[44px] rounded-[4px] border border-black"
                    >
                        Más información
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
