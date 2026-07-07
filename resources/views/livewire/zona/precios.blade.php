<div class="w-full bg-white" x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0">
    <div class="bg-white pb-[61px] pt-[24px]">
        <nav class="max-w-[1224px] mx-auto px-4 sm:px-6 min-[1200px]:px-0 text-black font-montserrat text-[12px] leading-[150%] flex items-center gap-1">
            <a wire:navigate href="{{ url('/') }}" class="text-black font-inter text-[12px] font-medium leading-normal whitespace-nowrap">Inicio</a>
            <span class="text-black font-inter text-[12px] font-light leading-normal">|</span>
            <span class="text-black font-inter text-[12px] font-light leading-normal whitespace-nowrap">Lista de precios</span>
        </nav>
    </div>

    <div class="max-w-[1224px] mx-auto px-4 sm:px-6 min-[1200px]:px-0 pb-[80px]">

        @if($vigente)

            {{-- TABLA DESKTOP --}}
            <div class="hidden rounded-t-[6px] min-[1200px]:block">
                <table class="w-full border-collapse rounded-t-[6px]">
                    <thead class="rounded-t-[6px]">
                        <tr class="bg-[#f8f9f9] h-[44px] text-black font-inter text-[16px] font-normal leading-normal">
                            <th class="w-[80px]"></th>
                            <th class="text-left pl-[74px] w-[450px]">Nombre</th>
                            <th class="text-left">Formato</th>
                            <th class="text-left pl-[44px]">Peso</th>
                            <th class="text-right pr-[22px]"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr><td colspan="5" class="h-[18px]"></td></tr>

                        @php
                            $extension = $vigente->archivo ? strtoupper(pathinfo($vigente->archivo, PATHINFO_EXTENSION)) : '-';
                            $disk = Storage::disk('public');
                            $peso = ($vigente->archivo && $disk->exists($vigente->archivo))
                                ? round($disk->size($vigente->archivo) / 1024) . ' kb'
                                : '-';
                        @endphp

                        <tr>
                            <td class="w-[80px] h-[73px]">
                                <div class="flex items-center justify-center">
                                    <div class="w-[80px] h-[80px] flex items-center justify-center rounded border border-gray-200">
                                        <svg width="43" height="43" viewBox="0 0 43 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M25.0827 3.58337V10.75C25.0827 11.7004 25.4602 12.6118 26.1322 13.2838C26.8042 13.9558 27.7157 14.3334 28.666 14.3334H35.8327M17.916 16.125H14.3327M28.666 23.2917H14.3327M28.666 30.4584H14.3327M26.8743 3.58337H10.7493C9.79899 3.58337 8.88755 3.9609 8.21555 4.63291C7.54354 5.30491 7.16602 6.21635 7.16602 7.16671V35.8334C7.16602 36.7837 7.54354 37.6952 8.21555 38.3672C8.88755 39.0392 9.79899 39.4167 10.7493 39.4167H32.2493C33.1997 39.4167 34.1111 39.0392 34.7831 38.3672C35.4552 37.6952 35.8327 36.7837 35.8327 35.8334V12.5417L26.8743 3.58337Z" stroke="#018637" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </td>
                            <td class="pl-[74px] w-[450px] text-black font-inter text-[16px] font-normal leading-[25px]">
                                {{ $vigente->title }}
                            </td>
                            <td class="text-black font-inter text-[16px] font-normal leading-normal">
                                {{ $extension }}
                            </td>
                            <td class="pl-[44px] text-black font-inter text-[16px] font-normal leading-normal">
                                {{ $peso }}
                            </td>
                            <td>
                                @if($vigente->archivo)
                                <div class="flex justify-end gap-[12px] pr-[22px]">
                                    <a href="{{ Storage::url($vigente->archivo) }}" target="_blank"
                                       class="rounded-[40px] border border-[#23378C] w-[184px] h-[40px] bg-white text-[#23378C] text-center font-semibold text-[14px] flex items-center justify-center">
                                        Ver online
                                    </a>
                                    <button wire:click="descargar({{ $vigente->id }})"
                                       class="w-[184px] h-[40px] rounded-[40px] cursor-pointer bg-[#23378C] text-white text-center font-semibold text-[14px] flex items-center justify-center">
                                        Descargar
                                    </button>
                                </div>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5" class="py-[10px]">
                                <div class="w-full h-[1px] bg-[#E5E5E5]"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- CARD MOBILE --}}
            <div class="grid gap-4 pb-4 min-[1200px]:hidden">
                @php
                    $extension = $vigente->archivo ? strtoupper(pathinfo($vigente->archivo, PATHINFO_EXTENSION)) : '-';
                    $disk = Storage::disk('public');
                    $peso = ($vigente->archivo && $disk->exists($vigente->archivo))
                        ? round($disk->size($vigente->archivo) / 1024) . ' kb'
                        : '-';
                @endphp
                <article class="rounded-[6px] border border-[#E5E5E5] bg-white p-4 sm:p-5">
                    <div class="flex items-start gap-4">
                        <div class="flex h-[72px] w-[72px] shrink-0 items-center justify-center rounded border border-gray-200">
                            <svg width="38" height="38" viewBox="0 0 43 43" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M25.0827 3.58337V10.75C25.0827 11.7004 25.4602 12.6118 26.1322 13.2838C26.8042 13.9558 27.7157 14.3334 28.666 14.3334H35.8327M17.916 16.125H14.3327M28.666 23.2917H14.3327M28.666 30.4584H14.3327M26.8743 3.58337H10.7493C9.79899 3.58337 8.88755 3.9609 8.21555 4.63291C7.54354 5.30491 7.16602 6.21635 7.16602 7.16671V35.8334C7.16602 36.7837 7.54354 37.6952 8.21555 38.3672C8.88755 39.0392 9.79899 39.4167 10.7493 39.4167H32.2493C33.1997 39.4167 34.1111 39.0392 34.7831 38.3672C35.4552 37.6952 35.8327 36.7837 35.8327 35.8334V12.5417L26.8743 3.58337Z" stroke="#018637" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-black font-inter text-[16px] font-normal leading-[24px] break-words">
                                {{ $vigente->title }}
                            </h3>
                            <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-[14px] font-inter leading-normal">
                                <div class="flex items-center gap-2">
                                    <span class="text-[#6B6B6B]">Formato</span>
                                    <span class="text-black">{{ $extension }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[#6B6B6B]">Peso</span>
                                    <span class="text-black">{{ $peso }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($vigente->archivo)
                    <div class="mt-4 h-[1px] w-full bg-[#E5E5E5]"></div>
                    <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ Storage::url($vigente->archivo) }}" target="_blank"
                           class="flex h-[40px] w-full items-center justify-center rounded-[40px] border border-[#23378C] bg-white text-center font-inter text-[14px] font-semibold text-[#23378C] sm:flex-1">
                            Ver online
                        </a>
                        <button wire:click="descargar({{ $vigente->id }})"
                           class="flex h-[40px] w-full cursor-pointer items-center justify-center rounded-[40px] bg-[#23378C] text-center font-inter text-[14px] font-semibold text-white sm:flex-1">
                            Descargar
                        </button>
                    </div>
                    @endif
                </article>
            </div>

        @else
            <div class="rounded-[8px] border border-gray-200 bg-gray-50 p-8 text-center">
                <p class="text-gray-500 font-inter text-[16px]">No hay una lista de precios vigente en este momento.</p>
            </div>
        @endif

    </div>
</div>
