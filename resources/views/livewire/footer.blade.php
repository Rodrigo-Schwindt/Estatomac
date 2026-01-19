<footer class="bg-[#000] relative text-white lg:h-[436px] flex flex-col">

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

    <div class="flex-1 lg:py-0 overflow-hidden">
        <div class="container mx-auto max-[1199px]:px-6">
            <div class="flex flex-col lg:flex-row lg:max-w-[1224px] lg:mx-auto lg:mt-[61px] gap-8 lg:gap-0 max-[1199px]:mt-[40px] max-[1199px]:pb-[40px]">
                
                @if($contactData && ($contactData->icono_1 || $contactData->icono_3))
                <div class="text-center lg:text-left w-full lg:w-auto max-[1199px]:order-1">
                    @if($contactData->icono_3)
                    <a wire:navigate href="{{ url('/') }}" class="inline-block">
                        <img src="{{ Storage::url($contactData->icono_3) }}" 
                             alt="Logo"
                             class="w-fit h-[65px] object-contain cursor-pointer mx-auto lg:mx-0">
                    </a>
                    @endif
                    @if($hasSocialMedia)
                    <div class="flex flex-col text-white ml-[131px] mt-[32px] lg:mt-0">
                        <div class="flex items-center gap-3 justify-center lg:justify-start">
                            @if($contactData->insta)
                            <a href="{{ $contactData->insta }}" target="_blank" class="hover:opacity-70">
                                <img src="{{ asset('instagram.svg') }}" class="w-[20px] h-[20px]">
                            </a>
                            @endif
                            
                            @if($contactData->facebook)
                            <a href="{{ $contactData->facebook }}" target="_blank" class="hover:opacity-70">
                                <img src="{{ asset('facebook.svg') }}" class="w-[20px] h-[20px]">
                            </a>
                            @endif
                    
                            @if($contactData->linkedin)
                            <a href="{{ $contactData->linkedin }}" target="_blank" class="hover:opacity-70">
                                <img src="{{ asset('linkedin.svg') }}" class="w-[20px] h-[20px] filter brightness-0 invert">
                            </a>
                            @endif
                    
                            @if($contactData->youtube)
                            <a class="mt-[4px]" href="{{ $contactData->youtube }}" target="_blank" class="hover:opacity-70">
                                <img src="{{ asset('youtube.svg') }}" class="w-[24px] h-[24px]">
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                <div class="flex flex-col">
                    <div class="text-center lg:text-left lg:pl-[145px] flex flex-col lg:flex-row gap-6 lg:gap-0 w-full lg:w-auto max-[1199px]:order-3 max-[1199px]:mt-[32px]">
                        <div class="mx-auto lg:mx-0">
                            <h3 class="text-white font-inter text-[20px] font-semibold leading-normal mb-[24px]">Secciones</h3>
                            <ul class="text-white font-inter text-[16px] font-normal leading-normal">
                                <li><a wire:navigate href="/" class="hover:underline block mb-[20px]">Inicio</a></li>
                                <li><a wire:navigate href="/nosotros" class="hover:underline block mb-[20px]">Nosotros</a></li>
                                <li><a wire:navigate href="/productos" class="hover:underline block">Productos</a></li>
                            </ul>
                        </div>
                    
                        <ul class="text-white font-inter text-[16px] font-normal leading-normal gap-[14px] lg:mt-[56px] w-fit lg:ml-[71px] mx-auto lg:mx-0">
                            <li><a wire:navigate href="/novedades" class="hover:underline block mb-[20px]">Novedades</a></li>
                            <li><a wire:navigate href="/contacto" class="hover:underline block mb-[20px]">Contacto</a></li>
                            <li x-data="{ openLoginModal: false }">
                                <a 
                                    @click.prevent="openLoginModal = true"
                                    href="#" 
                                    class="hover:underline block w-[119px] cursor-pointer">
                                    Acceso clientes
                                </a>
                    
                                {{-- Backdrop --}}
                                <div
                                    x-show="openLoginModal"
                                    x-transition.opacity
                                    @click="openLoginModal = false"
                                    class="fixed inset-0 bg-black/60 z-[100]"
                                    style="display: none;"
                                ></div>
                    
                                {{-- Modal --}}
                                <div
                                    x-show="openLoginModal"
                                    x-transition
                                    @click="openLoginModal = false"
                                    class="fixed inset-0 z-[101] flex items-center justify-center px-4"
                                    style="display: none;"
                                >
                                    <div @click.stop class="bg-white w-full max-w-[420px] rounded-xl p-8">
                                        <div class="flex justify-between items-center mb-6">
                                            <h2 class="text-[28px] text-[#1b1b18] font-inter font-semibold">
                                                Acceso clientes
                                            </h2>
                                            <button @click="openLoginModal = false" class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                    
                                        <livewire:auth.login-form />
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

            <div class="text-center lg:text-left lg:pl-[145px] w-full lg:w-auto mt-[32px] max-[1199px]:order-4 max-[1199px]:mt-[32px] z-5 relative">
                <h3 class="text-white font-inter text-[20px] font-semibold leading-normal mb-[23px]">Suscribite al Newsletter</h3>

                <form wire:submit.prevent="subscribe" class="max-w-xs mx-auto lg:mx-0">
                    <div class="relative flex items-center w-[288px] h-[45px] border border-white rounded-[4px] p-2 mb-[34px] mx-auto lg:mx-0">
                        <input 
                            type="email"
                            wire:model="newsletterEmail"
                            placeholder="Email"
                            class="bg-transparent text-white placeholder-white/90 text-[14px] w-full focus:outline-none pl-[8px] z-5 relative"
                        />
                        <button 
                            type="submit" 
                            class="absolute z-10 right-3 text-[#E40044] cursor-pointer transition-transform duration-200 hover:translate-x-[3px] cursor-pointer flex items-center justify-center"
                            aria-label="Suscribirse">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" class="pointer-events-none">
                                <path d="M5 12H19M19 12L12 5M19 12L12 19"
                                      stroke="currentColor" stroke-width="2"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                
                    @error('newsletterEmail')
                        <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </form>
                
            
            </div>
                </div>


                <div class="text-center lg:text-left lg:ml-[95px] w-full lg:w-auto mt-[32px] lg:mt-0 max-[1199px]:order-2">
                    <h3 class="text-white font-inter text-[20px] font-semibold leading-normal mb-[19px]">
                        Contacto
                    </h3>
                
                    <div class="text-white font-montserrat text-[14px] flex flex-col gap-[20px] items-center lg:items-start mx-auto">

                        @if($contactData?->direction_adm)
                        <div class="flex items-start text-[#E40044] max-w-[348px] w-full max-[767px]:justify-center ">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-1">
                                <path d="M16.6663 8.33329C16.6663 13.3333 9.99967 18.3333 9.99967 18.3333C9.99967 18.3333 3.33301 13.3333 3.33301 8.33329C3.33301 6.56518 4.03539 4.86949 5.28563 3.61925C6.53587 2.36901 8.23156 1.66663 9.99967 1.66663C11.7678 1.66663 13.4635 2.36901 14.7137 3.61925C15.964 4.86949 16.6663 6.56518 16.6663 8.33329Z" stroke="#E40044" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10 10.8334C11.3807 10.8334 12.5 9.71409 12.5 8.33337C12.5 6.95266 11.3807 5.83337 10 5.83337C8.61929 5.83337 7.5 6.95266 7.5 8.33337C7.5 9.71409 8.61929 10.8334 10 10.8334Z" stroke="#E40044" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contactData->direction_adm) }}" target="_blank" class="text-white font-montserrat text-[16px] font-normal leading-[150%] ml-3 max-[767px]:ml-0">
                                {{ $contactData->direction_adm }}
                            </a>
                        </div>
                        @endif
                        
                        @if($contactData?->wssp)
                        <div class="flex items-start max-w-[318px] w-full max-[767px]:justify-center">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-1">
                                <path d="M16.1289 3.79297C14.4922 2.15234 12.3125 1.25 9.99609 1.25C5.21484 1.25 1.32422 5.14062 1.32422 9.92188C1.32422 11.4492 1.72266 12.9414 2.48047 14.2578L1.25 18.75L5.84766 17.543C7.11328 18.2344 8.53906 18.5977 9.99219 18.5977H9.99609C14.7734 18.5977 18.75 14.707 18.75 9.92578C18.75 7.60938 17.7656 5.43359 16.1289 3.79297ZM9.99609 17.1367C8.69922 17.1367 7.42969 16.7891 6.32422 16.1328L6.0625 15.9766L3.33594 16.6914L4.0625 14.0312L3.89062 13.7578C3.16797 12.6094 2.78906 11.2852 2.78906 9.92188C2.78906 5.94922 6.02344 2.71484 10 2.71484C11.9258 2.71484 13.7344 3.46484 15.0938 4.82812C16.4531 6.19141 17.2891 8 17.2852 9.92578C17.2852 13.9023 13.9688 17.1367 9.99609 17.1367ZM13.9492 11.7383C13.7344 11.6289 12.668 11.1055 12.4688 11.0352C12.2695 10.9609 12.125 10.9258 11.9805 11.1445C11.8359 11.3633 11.4219 11.8477 11.293 11.9961C11.168 12.1406 11.0391 12.1602 10.8242 12.0508C9.55078 11.4141 8.71484 10.9141 7.875 9.47266C7.65234 9.08984 8.09766 9.11719 8.51172 8.28906C8.58203 8.14453 8.54687 8.01953 8.49219 7.91016C8.4375 7.80078 8.00391 6.73438 7.82422 6.30078C7.64844 5.87891 7.46875 5.9375 7.33594 5.92969C7.21094 5.92188 7.06641 5.92188 6.92188 5.92188C6.77734 5.92188 6.54297 5.97656 6.34375 6.19141C6.14453 6.41016 5.58594 6.93359 5.58594 8C5.58594 9.06641 6.36328 10.0977 6.46875 10.2422C6.57812 10.3867 7.99609 12.5742 10.1719 13.5156C11.5469 14.1094 12.0859 14.1602 12.7734 14.0586C13.1914 13.9961 14.0547 13.5352 14.2344 13.0273C14.4141 12.5195 14.4141 12.0859 14.3594 11.9961C14.3086 11.8984 14.1641 11.8438 13.9492 11.7383Z" fill="#E40044"/>
                            </svg>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactData->wssp) }}" target="_blank" class="text-white font-montserrat text-[16px] font-normal leading-[150%] ml-3">
                                {{ $contactData->wssp }}
                            </a>
                        </div>
                        @endif
                        
                        @if($contactData?->phone_amd)
                        <div class="flex items-center max-w-[318px] w-full max-[767px]:justify-center">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-1">
                                <path d="M16.1289 3.79297C14.4922 2.15234 12.3125 1.25 9.99609 1.25C5.21484 1.25 1.32422 5.14062 1.32422 9.92188C1.32422 11.4492 1.72266 12.9414 2.48047 14.2578L1.25 18.75L5.84766 17.543C7.11328 18.2344 8.53906 18.5977 9.99219 18.5977H9.99609C14.7734 18.5977 18.75 14.707 18.75 9.92578C18.75 7.60938 17.7656 5.43359 16.1289 3.79297ZM9.99609 17.1367C8.69922 17.1367 7.42969 16.7891 6.32422 16.1328L6.0625 15.9766L3.33594 16.6914L4.0625 14.0312L3.89062 13.7578C3.16797 12.6094 2.78906 11.2852 2.78906 9.92188C2.78906 5.94922 6.02344 2.71484 10 2.71484C11.9258 2.71484 13.7344 3.46484 15.0938 4.82812C16.4531 6.19141 17.2891 8 17.2852 9.92578C17.2852 13.9023 13.9688 17.1367 9.99609 17.1367ZM13.9492 11.7383C13.7344 11.6289 12.668 11.1055 12.4688 11.0352C12.2695 10.9609 12.125 10.9258 11.9805 11.1445C11.8359 11.3633 11.4219 11.8477 11.293 11.9961C11.168 12.1406 11.0391 12.1602 10.8242 12.0508C9.55078 11.4141 8.71484 10.9141 7.875 9.47266C7.65234 9.08984 8.09766 9.11719 8.51172 8.28906C8.58203 8.14453 8.54687 8.01953 8.49219 7.91016C8.4375 7.80078 8.00391 6.73438 7.82422 6.30078C7.64844 5.87891 7.46875 5.9375 7.33594 5.92969C7.21094 5.92188 7.06641 5.92188 6.92188 5.92188C6.77734 5.92188 6.54297 5.97656 6.34375 6.19141C6.14453 6.41016 5.58594 6.93359 5.58594 8C5.58594 9.06641 6.36328 10.0977 6.46875 10.2422C6.57812 10.3867 7.99609 12.5742 10.1719 13.5156C11.5469 14.1094 12.0859 14.1602 12.7734 14.0586C13.1914 13.9961 14.0547 13.5352 14.2344 13.0273C14.4141 12.5195 14.4141 12.0859 14.3594 11.9961C14.3086 11.8984 14.1641 11.8438 13.9492 11.7383Z" fill="#E40044"/>
                            </svg>
                            <a href="tel:{{ $contactData->phone_amd }}" class="text-white font-montserrat text-[16px] font-normal leading-[150%] ml-3">
                                {{ $contactData->phone_amd }}
                            </a>
                        </div>
                        @endif
                        
                        @if($contactData?->mail_adm)
                        <div class="flex items-center max-w-[318px] w-full max-[767px]:justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" class="flex-shrink-0">
                                <path d="M16.667 3.33334H3.33366C2.41318 3.33334 1.66699 4.07954 1.66699 5.00001V15C1.66699 15.9205 2.41318 16.6667 3.33366 16.6667H16.667C17.5875 16.6667 18.3337 15.9205 18.3337 15V5.00001C18.3337 4.07954 17.5875 3.33334 16.667 3.33334Z" stroke="#E40044" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.3337 5.83334L10.8587 10.5833C10.6014 10.7445 10.3039 10.83 10.0003 10.83C9.69673 10.83 9.39926 10.7445 9.14199 10.5833L1.66699 5.83334" stroke="#E40044" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <a href="mailto:{{ $contactData->mail_adm }}" class="text-white font-montserrat text-[16px] font-normal leading-[150%] ml-3 break-all">
                                {{ $contactData->mail_adm }}
                            </a>
                        </div>
                        @endif
                        
                        @if($contactData?->direction_sale)
                        <div class="flex items-start max-w-[318px] w-full max-[767px]:justify-center">
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
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contactData->direction_sale) }}" target="_blank" class="text-white font-montserrat text-[16px] font-normal leading-[150%] ml-3">
                                {{ $contactData->direction_sale }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class=" py-4 lg:py-0 lg:h-[64px] flex items-center">
        <div class="container mx-auto  max-[1199px]:px-6">
            <div class=" flex flex-col lg:flex-row justify-between items-center gap-4 lg:gap-0 text-center lg:text-left lg:max-w-[1224px] lg:mx-auto w-full">
                <div class="text-white font-montserrat text-[14px] opacity-80 z-5 relative">
                    © Copyright 2026 Estalomac. Todos los derechos reservados
                </div>
                <div class="text-white font-karla text-xs lg:text-sm opacity-80 z-5 relative">
                    <a href="https://osole.com.ar" target="_blank" rel="noopener noreferrer">
                        By <strong>Osole</strong>
                    </a>                            
                </div>
            </div>
        </div>
    </div>
    <img src="{{ asset('fondo.png') }}" 
         alt="Footer Background"
         class="absolute bottom-0 z-0 -left-[450px] w-full h-[248px] object-contain pointer-events-none select-none">

</footer>