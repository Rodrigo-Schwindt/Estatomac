<footer class="bg-[#23378C] relative text-white lg:h-[391px] flex flex-col">

    <div 
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:toast.window="
            message = $event.detail.message;
            type = $event.detail.type;
            show = true;
            setTimeout(() => show = false, 3000);
        "
        x-show="show"
        x-transition
        class="fixed bottom-6 right-6 py-3 px-4 rounded shadow text-white"
        :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'"
        style="z-index:9999"
    >
        <span x-text="message"></span>
    </div>

    <div class="flex-1 py-8 lg:py-0 overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 lg:px-0">
            <div class="flex flex-col lg:flex-row lg:max-w-[1224px] lg:mx-auto lg:mt-[99px] gap-8 lg:gap-0"
                 x-data="{ animate: false }"
                 x-init="setTimeout(() => animate = true, 100)"
                 x-show="animate"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                
                @if($contactData && ($contactData->icono_1 || $contactData->icono_3))
                <div class="  w-full lg:w-[756px]">
                    @if($contactData->icono_3)
                    <a wire:navigate href="{{ url('/') }}" class="inline-block">
                        <img src="{{ Storage::url($contactData->icono_3) }}" 
                             alt="Logo"
                             class="w-fit h-[107px] object-contain cursor-pointer mx-auto lg:mx-0">
                    </a>
                    @endif
                    @if($hasSocialMedia)
                    <div class="flex flex-col lg:ml-2 text-white mt-6 lg:mt-[32px]">
                        <div class="flex items-center gap-3 justify-center lg:justify-start">
                            @if($contactData->insta)
                            <a href="{{ $contactData->insta }}" target="_blank" class="hover:opacity-70 transition-opacity">
                                <img src="{{ asset('instagram.svg') }}" class="w-[20px] h-[20px]">
                            </a>
                            @endif
                            
                            @if($contactData->facebook)
                            <a href="{{ $contactData->facebook }}" target="_blank" class="hover:opacity-70 transition-opacity">
                                <img src="{{ asset('facebook.svg') }}" class="w-[20px] h-[20px]">
                            </a>
                            @endif
                    
                            @if($contactData->linkedin)
                            <a href="{{ $contactData->linkedin }}" target="_blank" class="hover:opacity-70 transition-opacity">
                                <img src="{{ asset('linkedin.svg') }}" class="w-[20px] h-[20px] filter brightness-0 invert">
                            </a>
                            @endif
                    
                            @if($contactData->youtube)
                            <a class="mt-[4px]" href="{{ $contactData->youtube }}" target="_blank" class="hover:opacity-70 transition-opacity">
                                <img src="{{ asset('youtube.svg') }}" class="w-[24px] h-[24px]">
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                
                <div class="flex flex-col w-full lg:w-auto">
                    <div class="text-center lg:text-left lg:pl-[145px] flex flex-col sm:flex-row gap-[36px] sm:gap-12 lg:gap-0 w-full">
                        <div class="mx-auto lg:mx-0 w-full sm:w-auto">
                            <h3 class="text-white font-inter text-[20px] sm:text-[20px] font-semibold leading-normal mb-4 lg:mb-[24px]">Secciones</h3>
                            <ul class="text-white font-inter text-[16px] sm:text-[16px] font-normal leading-normal space-y-3 lg:space-y-0">
                                <li><a wire:navigate href="/nosotros" class="hover:underline block lg:mb-[20px]">Nosotros</a></li>
                                <li><a wire:navigate href="/productos" class="hover:underline block">Productos</a></li>
                            </ul>
                        </div>
                    
                        <ul class="text-white font-inter text-[16px] sm:text-[16px] font-normal leading-normal space-y-3 lg:gap-[14px] lg:mt-[56px] w-full sm:w-fit lg:ml-[36px] mx-auto lg:mx-0 text-center sm:text-left">
                            <li><a wire:navigate href="/novedades" class="hover:underline block lg:mb-[20px]">Novedades</a></li>
                            <li><a wire:navigate href="/contacto" class="hover:underline block lg:mb-[20px]">Contacto</a></li>
                           
                        </ul>
                    </div>

                
                </div>

                <div class="text-center lg:text-left lg:ml-[191px] w-full  mt-8 lg:mt-0 max-[650px]:mt-[0px]">
                    <h3 class="text-white font-inter text-[20px] sm:text-[20px] font-semibold leading-normal mb-4 lg:mb-[19px]">
                        Datos de contacto
                    </h3>
                
                    <div class="text-white font-montserrat text-[14px] flex flex-col gap-4 lg:gap-[20px] items-center lg:items-start mx-auto">

                        @if($contactData?->direction_adm)
                        <div class="flex items-start text-[#E40044] max-w-[348px] w-full justify-center lg:justify-start">
<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" class="mt-1" viewBox="0 0 20 20" fill="none">
  <path d="M16.6667 8.33317C16.6667 13.3332 10 18.3332 10 18.3332C10 18.3332 3.33337 13.3332 3.33337 8.33317C3.33337 6.56506 4.03575 4.86937 5.286 3.61913C6.53624 2.36888 8.23193 1.6665 10 1.6665C11.7682 1.6665 13.4638 2.36888 14.7141 3.61913C15.9643 4.86937 16.6667 6.56506 16.6667 8.33317Z" stroke="#018637" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M10 10.8335C11.3807 10.8335 12.5 9.71421 12.5 8.3335C12.5 6.95278 11.3807 5.8335 10 5.8335C8.61929 5.8335 7.5 6.95278 7.5 8.3335C7.5 9.71421 8.61929 10.8335 10 10.8335Z" stroke="#018637" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contactData->direction_adm) }}" target="_blank" class="text-white font-montserrat text-[14px] sm:text-[16px] font-normal leading-[150%] ml-3 break-words">
                                {{ $contactData->direction_adm }}
                            </a>
                        </div>
                        @endif
                        
                        @if($contactData?->phone_amd)
                        <div class="flex items-center max-w-[318px] w-full justify-center lg:justify-start">
<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
  <g clip-path="url(#clip0_5515_22)">
    <path d="M18.3333 14.0999V16.5999C18.3343 16.832 18.2867 17.0617 18.1937 17.2744C18.1008 17.487 17.9644 17.6779 17.7934 17.8348C17.6224 17.9917 17.4205 18.1112 17.2006 18.1855C16.9808 18.2599 16.7478 18.2875 16.5167 18.2666C13.9523 17.988 11.4892 17.1117 9.32498 15.7083C7.31151 14.4288 5.60443 12.7217 4.32499 10.7083C2.91663 8.53426 2.04019 6.05908 1.76665 3.48325C1.74583 3.25281 1.77321 3.02055 1.84707 2.80127C1.92092 2.58199 2.03963 2.38049 2.19562 2.2096C2.35162 2.03871 2.54149 1.90218 2.75314 1.80869C2.9648 1.7152 3.1936 1.6668 3.42499 1.66658H5.92499C6.32941 1.6626 6.72148 1.80582 7.02812 2.06953C7.33476 2.33324 7.53505 2.69946 7.59165 3.09992C7.69717 3.89997 7.89286 4.68552 8.17499 5.44158C8.2871 5.73985 8.31137 6.06401 8.24491 6.37565C8.17844 6.68729 8.02404 6.97334 7.79998 7.19992L6.74165 8.25825C7.92795 10.3445 9.65536 12.072 11.7417 13.2583L12.8 12.1999C13.0266 11.9759 13.3126 11.8215 13.6243 11.755C13.9359 11.6885 14.26 11.7128 14.5583 11.8249C15.3144 12.107 16.0999 12.3027 16.9 12.4083C17.3048 12.4654 17.6745 12.6693 17.9388 12.9812C18.203 13.2931 18.3435 13.6912 18.3333 14.0999Z" stroke="#018637" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  </g>
  <defs>
    <clipPath id="clip0_5515_22">
      <rect width="20" height="20" fill="white"/>
    </clipPath>
  </defs>
</svg>
                            <a href="tel:{{ $contactData->phone_amd }}" class="text-white font-montserrat text-[14px] sm:text-[16px] font-normal leading-[150%] ml-3">
                                {{ $contactData->phone_amd }}
                            </a>
                        </div>
                        @endif
                        @if($contactData?->mail_adm)
                        <div class="flex items-center max-w-[318px] w-full justify-center lg:justify-start">
<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
  <path d="M16.6666 3.3335H3.33329C2.41282 3.3335 1.66663 4.07969 1.66663 5.00016V15.0002C1.66663 15.9206 2.41282 16.6668 3.33329 16.6668H16.6666C17.5871 16.6668 18.3333 15.9206 18.3333 15.0002V5.00016C18.3333 4.07969 17.5871 3.3335 16.6666 3.3335Z" stroke="#018637" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M18.3333 5.8335L10.8583 10.5835C10.601 10.7447 10.3036 10.8302 9.99996 10.8302C9.69636 10.8302 9.3989 10.7447 9.14163 10.5835L1.66663 5.8335" stroke="#018637" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                            <a href="mailto:{{ $contactData->mail_adm }}" class="text-white font-montserrat text-[14px] sm:text-[16px] font-normal leading-[150%] ml-3 break-all">
                                {{ $contactData->mail_adm }}
                            </a>
                        </div>
                        @endif
                        @if($contactData?->wssp)
                        <div class="flex items-start max-w-[318px] w-full justify-center lg:justify-start">
<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
  <path fill-rule="evenodd" clip-rule="evenodd" d="M14.5823 11.985C14.3328 11.8608 13.1095 11.2625 12.8817 11.1792C12.6539 11.0967 12.4881 11.0558 12.3215 11.3042C12.1557 11.5508 11.6793 12.1092 11.5344 12.2742C11.3887 12.44 11.2438 12.46 10.9952 12.3367C10.7465 12.2117 9.94429 11.9508 8.99391 11.1075C8.25453 10.4509 7.75464 9.64002 7.60978 9.39169C7.46492 9.14419 7.59387 9.01002 7.71863 8.88669C7.83084 8.77585 7.96732 8.59752 8.09209 8.45335C8.21685 8.30835 8.25788 8.20502 8.34078 8.03919C8.42451 7.87419 8.38265 7.73002 8.31985 7.60585C8.25788 7.48169 7.7605 6.26252 7.55284 5.76669C7.35104 5.28419 7.14589 5.35003 6.99349 5.34169C6.8478 5.33503 6.682 5.33336 6.51621 5.33336C6.35041 5.33336 6.08079 5.39503 5.85303 5.64336C5.62444 5.89086 4.98219 6.49002 4.98219 7.70919C4.98219 8.92752 5.87313 10.105 5.99789 10.2709C6.12266 10.4359 7.75213 12.9375 10.2482 14.01C10.8428 14.265 11.3058 14.4175 11.6667 14.5308C12.2629 14.72 12.8055 14.6933 13.2342 14.6292C13.7115 14.5583 14.7063 14.03 14.9139 13.4517C15.1208 12.8733 15.1208 12.3775 15.0588 12.2742C14.9968 12.1708 14.831 12.1092 14.5815 11.985H14.5823ZM10.0423 18.1542H10.0389C8.55634 18.1544 7.10099 17.7578 5.8254 17.0058L5.52396 16.8275L2.39062 17.6458L3.22712 14.6058L3.03035 14.2942C2.20149 12.9811 1.76286 11.4615 1.76512 9.91085C1.7668 5.36919 5.47958 1.6742 10.0456 1.6742C12.2562 1.6742 14.3345 2.53253 15.897 4.08919C16.6676 4.85301 17.2785 5.76133 17.6941 6.7616C18.1098 7.76188 18.322 8.83425 18.3186 9.91668C18.3169 14.4583 14.6041 18.1542 10.0423 18.1542ZM17.086 2.9067C16.1634 1.98247 15.0657 1.24965 13.8564 0.7507C12.6472 0.251754 11.3505 -0.00339687 10.0414 3.41479e-05C4.55347 3.41479e-05 0.0854091 4.44586 0.0837344 9.91002C0.0811914 11.649 0.539563 13.3578 1.4126 14.8642L0 20L5.27861 18.6217C6.73884 19.4134 8.37519 19.8283 10.0381 19.8283H10.0423C15.5302 19.8283 19.9983 15.3825 20 9.91752C20.004 8.61525 19.7485 7.32511 19.2484 6.12172C18.7482 4.91833 18.0132 3.82559 17.086 2.9067Z" fill="#018637"/>
</svg>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactData->wssp) }}" target="_blank" class="text-white font-montserrat text-[14px] sm:text-[16px] font-normal leading-[150%] ml-3">
                                {{ $contactData->wssp }}
                            </a>
                        </div>
                        @endif
                        
                        
                        
                        @if($contactData?->direction_sale)
                        <div class="flex items-start max-w-[318px] w-full justify-center lg:justify-start">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-1">
                                <g clip-path="url(#clip0_4003_5328)">
                                    <path d="M10.0003 4.99999V9.99999L13.3337 11.6667M18.3337 9.99999C18.3337 14.6024 14.6027 18.3333 10.0003 18.3333C5.39795 18.3333 1.66699 14.6024 1.66699 9.99999C1.66699 5.39762 5.39795 1.66666 10.0003 1.66666C14.6027 1.66666 18.3337 5.39762 18.3337 9.99999Z" stroke="#E40044" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_4003_5328">
                                        <rect width="20" height="20" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contactData->direction_sale) }}" target="_blank" class="text-white font-montserrat text-[14px] sm:text-[16px] font-normal leading-[150%] ml-3 break-words">
                                {{ $contactData->direction_sale }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-6 lg:py-0 lg:h-[64px] flex items-center border-t border-white/10 lg:border-0"
         x-data="{ animate: false }"
         x-init="setTimeout(() => animate = true, 300)"
         x-show="animate"
         x-transition:enter="transition ease-out duration-400"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="container mx-auto px-4 sm:px-6 lg:px-0">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-3 lg:gap-0 text-center lg:text-left lg:max-w-[1224px] lg:mx-auto w-full">
                <div class="text-white font-montserrat text-[12px] sm:text-[14px] opacity-80 z-5 relative">
                    © Copyright 2026 Todotex. Todos los derechos reservados
                </div>
                <div class="text-white font-karla text-[11px] sm:text-[12px] lg:text-sm opacity-80 z-5 relative">
                    <a href="https://osole.com.ar" target="_blank" rel="noopener noreferrer">
                        By <strong>Osole</strong>
                    </a>                            
                </div>
            </div>
        </div>
    </div>
    
    
</footer>