@php
    use Carbon\Carbon;
@endphp

<div
    x-data="{
        show: false,
        perPage: @js($perPage),
        updatePerPage() {
            const nextPerPage = window.matchMedia('(max-width: 767px)').matches ? 3 : 6;

            if (this.perPage !== nextPerPage) {
                this.perPage = nextPerPage;
                this.$wire.setPerPage(nextPerPage);
            }
        }
    }"
    x-init="
        updatePerPage();
        setTimeout(() => show = true, 50);
        window.addEventListener('resize', () => updatePerPage());
    "
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 transform -translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0"
>

    <div class="bg-[#F5F5F5]  h-[150px]">
        <div class="max-w-[1224px] mx-auto">
                <div class=" max-[1199px]:px-4 ">
            <div class="max-w-[1224px] mx-auto pt-[16px]">
                <nav class="text-white font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] flex items-center gap-1">
                    <a wire:navigate href="{{ url('/') }}" class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] font-bold leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Inicio</a>
                    <span class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">|</span>
                    <span class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Novedades</span>
                </nav>
            </div>
        </div>
        <h1 class="text-[40px] pt-[43px] font-bold lg:px-0 px-4">Novedades</h1>
        </div>
    </div>

    <section class="bg-white pt-[80px] pb-[120px] max-[1199px]:pt-16 max-[1199px]:pb-20 max-[1199px]:px-4 max-[639px]:pt-12 max-[639px]:pb-16">
        <div class="max-w-[1224px] mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-[24px] max-[1199px]:grid-cols-2 max-[767px]:grid-cols-1 max-[1199px]:gap-5">
                @foreach ($novedades as $item)
                <a href="/novedades/{{ $item->id }}"
                    class="block rounded-[4px] border border-[#DDDDE0] w-full bg-white overflow-hidden cursor-pointer group transition-all duration-300  hover:border-[#23378C]/30  max-[991px]:h-[500px] max-[767px]:h-auto max-[639px]:h-auto">

                     <div class="w-full h-[263px] overflow-hidden max-[1199px]:h-[240px] max-[991px]:h-[220px] max-[767px]:h-[240px] max-[639px]:h-[200px]">
                         <img src="{{ Storage::url($item->image) }}"
                              alt="{{ $item->title }}"
                              loading="lazy"
                              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                     </div>

                     <div class="flex flex-col px-[16px] pb-[12px] justify-between h-[243px] max-[1199px]:h-auto max-[991px]:h-[280px] max-[767px]:h-auto max-[639px]:h-auto max-[639px]:p-4 max-[639px]:pb-3">
                         <div>
                             <p class="text-[#23378C] font-inter text-[14px] font-bold leading-[22px] mb-[10px] mt-[18px] max-[1199px]:mt-4 max-[767px]:text-[15px] max-[639px]:text-[13px] max-[639px]:mt-0 max-[639px]:mb-2 transition-colors duration-300">
                                 {{ $item->novcategories->first()->title ?? 'Novedad' }}
                             </p>

                             <h3 class="text-black font-montserrat text-[21px] font-medium leading-[120%] mb-[6px] line-clamp-2 max-[1199px]:text-[22px] max-[991px]:text-[20px] max-[767px]:text-[22px] max-[639px]:text-[18px] max-[639px]:mb-3 group-hover:text-[#23378C] transition-colors duration-300">
                                 {{ $item->title }}
                             </h3>

                             <p class="text-black font-inter text-[16px] font-normal leading-[25px] line-clamp-3 max-[1199px]:text-[15px] max-[767px]:text-[16px] max-[639px]:text-[14px] max-[639px]:leading-[22px]">
                                 {{ preg_replace('/(&nbsp;)+$/', '', strip_tags($item->description)) }}
                             </p>
                         </div>

                         <span class="text-black font-inter text-[16px] leading-normal hover:underline max-[1199px]:text-[15px] max-[767px]:text-[16px] max-[639px]:text-[14px] max-[639px]:mt-4 max-[767px]:mt-3 group-hover:text-[#23378C] transition-colors duration-300 inline-block">
                             Leer más
                         </span>
                     </div>
                 </a>
                @endforeach
            </div>

            @if ($novedades->hasPages())
                <div class="mt-[48px] flex justify-center max-[639px]:mt-8">
                    {{ $novedades->links() }}
                </div>
            @endif
        </div>
    </section>

</div>
