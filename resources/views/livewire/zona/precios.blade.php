<div class="w-full bg-white max-w-[1224px] mx-auto" x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0">
    <div class="bg-white  pb-[61px] pt-[24px]">
        <nav class="max-w-[1224px] mx-auto text-black font-montserrat text-[12px] leading-[150%] flex items-center gap-1">
            <a wire:navigate href="{{ url('/') }}" class="text-black font-inter text-[12px] font-medium leading-normal drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Inicio</a>
            <span class="text-black font-inter text-[12px] font-light leading-normal drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">›</span>
            <span class="text-black font-inter text-[12px] font-light leading-normal drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Lista de precios</span>
        </nav>
    </div>
    <table class="w-full border-collapse rounded-t-[6px]">
        <thead class="rounded-t-[6px]">
            <tr class="bg-black h-[52px] text-white font-inter text-[16px] font-medium leading-normal ">
                <th class="w-[80px]"></th>
                <th class="text-left pl-[74px]">Nombre</th>
                <th class="text-left">Formato</th>
                <th class="text-left pl-[44px]">Peso</th>
                <th class="text-right pr-[22px]"></th>
            </tr>
        </thead>

        <tbody class="bg-white">
            <tr>
                <td colspan="5" class="h-[18px]"></td>
            </tr>

            @foreach($precios as $precio)
                @php
                    $extension = strtoupper(pathinfo($precio->archivo, PATHINFO_EXTENSION));
                    $disk = Storage::disk('public');

$peso = $disk->exists($precio->archivo)
    ? round($disk->size($precio->archivo) / 1024) . ' kb'
    : '-';
                @endphp

                <tr>
                    <!-- Icono -->
                    <td class="w-[80px] h-[73px]">
                        <div class="flex items-center justify-center">
                            <div class="w-[80px] h-[80px] flex items-center justify-center rounded bg-[#F8F8F8]">
                                <svg width="43" height="43" viewBox="0 0 43 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M25.0827 3.58337V10.75C25.0827 11.7004 25.4602 12.6118 26.1322 13.2838C26.8042 13.9558 27.7157 14.3334 28.666 14.3334H35.8327M17.916 16.125H14.3327M28.666 23.2917H14.3327M28.666 30.4584H14.3327M26.8743 3.58337H10.7493C9.79899 3.58337 8.88755 3.9609 8.21555 4.63291C7.54354 5.30491 7.16602 6.21635 7.16602 7.16671V35.8334C7.16602 36.7837 7.54354 37.6952 8.21555 38.3672C8.88755 39.0392 9.79899 39.4167 10.7493 39.4167H32.2493C33.1997 39.4167 34.1111 39.0392 34.7831 38.3672C35.4552 37.6952 35.8327 36.7837 35.8327 35.8334V12.5417L26.8743 3.58337Z" stroke="#E40044" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    
                            </div>
                        </div>
                    </td>

                    <!-- Nombre -->
                    <td class="pl-[74px] text-black font-inter text-[16px] font-normal leading-[25px]">
                        {{ $precio->title }}
                    </td>

                    <!-- Formato -->
                    <td class="text-black font-inter text-[16px] font-normal leading-normal">
                        {{ $extension }}
                    </td>

                    <!-- Peso -->
                    <td class="pl-[44px] text-black font-inter text-[16px] font-normal leading-normal">
                        {{ $peso }}
                    </td>

                    <td>
                        <div class="flex justify-end gap-[12px]">
                            <a
                                href="{{ Storage::url($precio->archivo) }}"
                                target="_blank"
                                class="rounded-[4px] border border-[#E40044] w-[127px] h-[44px] bg-white text-[#E40044] text-center font-inter text-[14px] font-normal leading-normal uppercase flex items-center justify-center"
                            >
                                VER ONLINE
                            </a>

                            <button
                                wire:click="descargar({{ $precio->id }})"
                                class="w-[127px] h-[44px] rounded-[4px] bg-[#E40044] text-white text-center font-inter text-[14px] font-normal leading-normal uppercase flex items-center justify-center "
                            >
                                DESCARGAR
                            </button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="5" class="py-[10px]">
                        <div class="w-full h-[1px] bg-[#E5E5E5]"></div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
