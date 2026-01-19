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
    <section class="relative w-full h-[380px] max-[767px]:h-[300px] max-[639px]:h-[250px]">
        <img src="{{ asset('storage/' . $banner->image_banner) }}" 
             alt="{{ $banner->title }}"
             class="w-full h-full object-cover grayscale-[100%] contrast-155">

        <div class="absolute top-[114px] left-0 right-0 z-30 max-[1199px]:top-[80px]">
            <div class="max-w-[1224px] mx-auto">
                <nav class="text-white font-montserrat text-[12px] leading-[150%] flex items-center gap-1">
                    <a wire:navigate href="{{ url('/') }}" class="text-white font-montserrat text-[12px] font-bold leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Inicio</a>
                    <span class="text-white font-montserrat text-[12px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">›</span>
                    <span class="text-white font-montserrat text-[12px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Novedades</span>
                </nav>
            </div>
        </div>

        <div class="absolute inset-0 bg-[rgba(170,65,65,0.5)]"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-black/20 to-black/20"></div>


        <div class="absolute inset-0 flex top-[245px] max-w-[1224px] mx-auto">
            <h1 class="text-white font-inter text-[40px] font-bold leading-normal
                       max-[767px]:text-[36px] max-[639px]:text-[28px] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">
                Novedades
            </h1>
        </div>
    </section>
    @endif


    <section class="bg-white pt-[80px] pb-[120px]">
        <div class="max-w-[1224px] mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-[24px]">
                @foreach ($novedades as $item)
                <a href="/novedades/{{ $item->id }}"
                    class="block rounded-[4px] border border-[#DDDDE0] w-full bg-white rounded-[4px] overflow-hidden cursor-pointer group max-[991px]:h-[500px] max-[767px]:h-[480px] max-[639px]:h-auto">

                     <div class="w-full h-[263px] overflow-hidden max-[991px]:h-[260px] max-[767px]:h-[240px] max-[639px]:h-[220px]">
                         <img src="{{ Storage::url($item->image) }}"
                              alt="{{ $item->title }}"
                              loading="lazy"
                              class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                     </div>

                     <div class="flex flex-col  px-[16px] pb-[12px] justify-between h-[243px] max-[991px]:h-[240px] max-[767px]:h-[240px] max-[639px]:h-auto max-[639px]:p-4">
                         <div>
                             <p class="text-[#E40044] font-inter text-[14px] font-bold leading-[22px] mb-[10px] mt-[18px] max-[767px]:text-[15px] max-[639px]:text-[14px] max-[639px]:mt-0">
                                 {{ $item->novcategories->first()->title ?? 'Novedad' }}
                             </p>

                             <h3 class="text-black font-montserrat text-[24px] font-medium leading-[120%] mb-[6px] line-clamp-2 max-[991px]:text-[22px] max-[767px]:text-[20px] max-[639px]:text-[18px] max-[639px]:mb-3">
                                 {{ $item->title }}
                             </h3>

                             <p class=" text-black  font-inter text-[16px] font-normal leading-[25px] line-clamp-3 max-[767px]:text-[15px] max-[639px]:text-[14px]">
                                 {{ preg_replace('/(&nbsp;)+$/', '', strip_tags($item->description)) }}
                             </p>
                         </div>
                         
                         <span class="text-black font-inter text-[16px] font-bold leading-normal hover:underline max-[767px]:text-[15px] max-[639px]:text-[14px] max-[639px]:mt-3">
                             Leer más
                         </span>
                     </div>
                 </a>
                @endforeach
            </div>
        </div>
    </section>
    

</div>


</div>