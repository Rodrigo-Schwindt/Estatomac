@php
    use Carbon\Carbon;
@endphp

<div class="w-full"
     x-data="{ show: false }"
     x-init="setTimeout(() => show = true, 50)"
     x-show="show"
     x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="opacity-0 transform -translate-y-4"
     x-transition:enter-end="opacity-100 transform translate-y-0">

    <nav class="max-w-[1224px] mx-auto text-black font-montserrat text-[12px] leading-[150%] flex items-center gap-1 mt-[24px] max-[1199px]:px-4 max-[1199px]:flex-wrap">
        <a wire:navigate href="{{ url('/') }}" class="text-black font-montserrat text-[12px] font-bold leading-[150%]">Inicio</a>
        <span class="text-black font-montserrat text-[12px] leading-[150%]">›</span>
        <a wire:navigate href="/novedades" class="text-black font-montserrat text-[12px] font-bold leading-[150%]">Novedades</a>
        <span class="text-black font-montserrat text-[12px] leading-[150%]">›</span>
        <span class="text-black font-montserrat text-[12px] leading-[150%]">{{ $novedad->title }}</span>
    </nav>

    <div class="max-w-[1224px] mx-auto flex  justify-center">
        
        <div class="mt-[50px]">
            @if($showDetail)
                <div class="w-full md:w-[900px] flex flex-col max-[1199px]:w-full">
                    <div class="max-w-[900px] max-h-[450px] mb-[40px] flex justify-center max-[1199px]:mb-[24px] max-[1199px]:max-w-full">
                        <img 
                            src="{{ Storage::url($novedad->image) }}" 
                            alt="{{ $novedad->title }}"
                            class="w-full max-h-[422px] object-cover max-[1199px]:max-h-[300px]"
                        >
                    </div>

                    <p class="text-[#E40044] font-Inter text-[18px] font-normal leading-[120%] max-[1199px]:text-[14px]">
                        {{ $novedad->novcategories->first()->title ?? 'Novedad' }}
                    </p>

                    <h1 class="text-black font-Inter text-[34px] mt-[8px] font-bold leading-[120%] w-[724px] mb-[24px] max-[1199px]:w-full max-[1199px]:text-[24px] max-[1199px]:mb-[16px]">
                        {{ $novedad->title }}
                    </h1>

                    <div class="text-[#111] font-Inter text-[18px] font-normal leading-[150%] w-[786px] content-description max-[1199px]:w-full max-[1199px]:text-[14px]">
                        {!! str_replace('&nbsp;', ' ', $novedad->description) !!}
                    </div>
                </div>
                @endif
                
        </div>
        </div>
    </div>
</div>