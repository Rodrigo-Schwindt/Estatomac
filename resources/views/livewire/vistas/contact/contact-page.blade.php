<div
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, 50)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 transform -translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0">

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
                    <span class="text-white font-montserrat text-[12px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Nosotros</span>
                </nav>
            </div>
        </div>

        <div class="absolute inset-0 bg-[rgba(170,65,65,0.5)]"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-black/20 to-black/20"></div>


        <div class="absolute inset-0 flex top-[245px] max-w-[1224px] mx-auto">
            <h1 class="text-white font-inter text-[40px] font-bold leading-normal
                       max-[767px]:text-[36px] max-[639px]:text-[28px] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">
                {{ $banner->title }}
            </h1>
        </div>
    </section>
    @endif

    <div >
        @if($successMessage)
        <div class="max-w-[1224px] mx-auto mt-6 mb-6 sm:mb-8 bg-green-50 border border-green-200 text-green-800 px-4 sm:px-6 py-4 rounded-lg flex items-center animate-fade-in" id="success-message">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ $successMessage }}
        </div>
        @endif

        @if($errorMessage)
        <div class="max-w-[1224px] mx-auto mb-6 mt-6 sm:mb-8 bg-red-50 border border-red-200 text-red-800 px-4 sm:px-6 py-4 rounded-lg flex items-center animate-fade-in">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ $errorMessage }}
        </div>
        @endif

        <div class="max-w-[1224px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-[24px] max-[1199px]:flex max-[1199px]:flex-col max-[1199px]:px-4">

            <div class="lg:col-span-4 max-[1199px]:order-2">
                <div class="max-[1199px]:w-full mt-[84px] max-[1199px]:mt-[40px]">
                    @if($contact?->direction_adm)
                    <div class="flex items-start mb-5 sm:mb-[20px] w-[345px] max-[1199px]:w-full">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-1">
                            <path d="M16.6663 8.33329C16.6663 13.3333 9.99967 18.3333 9.99967 18.3333C9.99967 18.3333 3.33301 13.3333 3.33301 8.33329C3.33301 6.56518 4.03539 4.86949 5.28563 3.61925C6.53587 2.36901 8.23156 1.66663 9.99967 1.66663C11.7678 1.66663 13.4635 2.36901 14.7137 3.61925C15.964 4.86949 16.6663 6.56518 16.6663 8.33329Z" stroke="#BA2025" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10 10.8334C11.3807 10.8334 12.5 9.71409 12.5 8.33337C12.5 6.95266 11.3807 5.83337 10 5.83337C8.61929 5.83337 7.5 6.95266 7.5 8.33337C7.5 9.71409 8.61929 10.8334 10 10.8334Z" stroke="#BA2025" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contact->direction_adm) }}" target="_blank" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] ml-3 max-[1199px]:text-[14px]">
                            {{ $contact->direction_adm }}
                        </a>
                    </div>
                    @endif
            
                    @if($contact?->wssp)
                    <div class="flex items-start mb-5 sm:mb-[20px] w-[318px] max-[1199px]:w-full">
                        <svg width="22" height="22" class="mb-1" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-1">
                            <path d="M16.1289 3.79297C14.4922 2.15234 12.3125 1.25 9.99609 1.25C5.21484 1.25 1.32422 5.14062 1.32422 9.92188C1.32422 11.4492 1.72266 12.9414 2.48047 14.2578L1.25 18.75L5.84766 17.543C7.11328 18.2344 8.53906 18.5977 9.99219 18.5977H9.99609C14.7734 18.5977 18.75 14.707 18.75 9.92578C18.75 7.60938 17.7656 5.43359 16.1289 3.79297ZM9.99609 17.1367C8.69922 17.1367 7.42969 16.7891 6.32422 16.1328L6.0625 15.9766L3.33594 16.6914L4.0625 14.0312L3.89062 13.7578C3.16797 12.6094 2.78906 11.2852 2.78906 9.92188C2.78906 5.94922 6.02344 2.71484 10 2.71484C11.9258 2.71484 13.7344 3.46484 15.0938 4.82812C16.4531 6.19141 17.2891 8 17.2852 9.92578C17.2852 13.9023 13.9688 17.1367 9.99609 17.1367ZM13.9492 11.7383C13.7344 11.6289 12.668 11.1055 12.4688 11.0352C12.2695 10.9609 12.125 10.9258 11.9805 11.1445C11.8359 11.3633 11.4219 11.8477 11.293 11.9961C11.168 12.1406 11.0391 12.1602 10.8242 12.0508C9.55078 11.4141 8.71484 10.9141 7.875 9.47266C7.65234 9.08984 8.09766 9.11719 8.51172 8.28906C8.58203 8.14453 8.54687 8.01953 8.49219 7.91016C8.4375 7.80078 8.00391 6.73438 7.82422 6.30078C7.64844 5.87891 7.46875 5.9375 7.33594 5.92969C7.21094 5.92188 7.06641 5.92188 6.92188 5.92188C6.77734 5.92188 6.54297 5.97656 6.34375 6.19141C6.14453 6.41016 5.58594 6.93359 5.58594 8C5.58594 9.06641 6.36328 10.0977 6.46875 10.2422C6.57812 10.3867 7.99609 12.5742 10.1719 13.5156C11.5469 14.1094 12.0859 14.1602 12.7734 14.0586C13.1914 13.9961 14.0547 13.5352 14.2344 13.0273C14.4141 12.5195 14.4141 12.0859 14.3594 11.9961C14.3086 11.8984 14.1641 11.8438 13.9492 11.7383Z" fill="#BA2025"/>
                        </svg>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->wssp) }}" target="_blank" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] ml-3 max-[1199px]:text-[14px]">
                            {{ $contact->wssp }}
                        </a>
                    </div>
                    @endif
                    
                    @if($contact?->phone_amd)
                    <div class="flex items-center mb-5 sm:mb-[20px]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" class="flex-shrink-0">
                            <g clip-path="url(#clip0_6030_3600)">
                                <path d="M11.527 13.8067C11.6991 13.8857 11.893 13.9038 12.0767 13.8579C12.2605 13.812 12.4231 13.7048 12.5378 13.5542L12.8337 13.1667C12.9889 12.9597 13.1902 12.7917 13.4216 12.6759C13.6531 12.5602 13.9082 12.5 14.167 12.5H16.667C17.109 12.5 17.5329 12.6756 17.8455 12.9881C18.1581 13.3007 18.3337 13.7246 18.3337 14.1667V16.6667C18.3337 17.1087 18.1581 17.5326 17.8455 17.8452C17.5329 18.1577 17.109 18.3333 16.667 18.3333C12.6887 18.3333 8.87343 16.753 6.06039 13.9399C3.24734 11.1269 1.66699 7.31157 1.66699 3.33332C1.66699 2.8913 1.84259 2.46737 2.15515 2.15481C2.46771 1.84225 2.89163 1.66666 3.33366 1.66666H5.83366C6.27569 1.66666 6.69961 1.84225 7.01217 2.15481C7.32473 2.46737 7.50032 2.8913 7.50032 3.33332V5.83332C7.50032 6.09206 7.44008 6.34725 7.32437 6.57868C7.20866 6.8101 7.04065 7.01141 6.83366 7.16666L6.44366 7.45916C6.29067 7.57597 6.18284 7.74214 6.13848 7.92945C6.09413 8.11675 6.11598 8.31364 6.20032 8.48666C7.33923 10.7999 9.21234 12.6707 11.527 13.8067Z" stroke="#BA2025" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_6030_3600">
                                    <rect width="20" height="20" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <a href="tel:{{ $contact->phone_amd }}" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] ml-3 max-[1199px]:text-[14px]">
                            {{ $contact->phone_amd }}
                        </a>
                    </div>
                    @endif
                    
                    @if($contact?->mail_adm)
                    <div class="flex items-center mb-5 sm:mb-[20px]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" class="flex-shrink-0">
                            <path d="M16.667 3.33334H3.33366C2.41318 3.33334 1.66699 4.07954 1.66699 5.00001V15C1.66699 15.9205 2.41318 16.6667 3.33366 16.6667H16.667C17.5875 16.6667 18.3337 15.9205 18.3337 15V5.00001C18.3337 4.07954 17.5875 3.33334 16.667 3.33334Z" stroke="#BA2025" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18.3337 5.83334L10.8587 10.5833C10.6014 10.7445 10.3039 10.83 10.0003 10.83C9.69673 10.83 9.39926 10.7445 9.14199 10.5833L1.66699 5.83334" stroke="#BA2025" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <a href="mailto:{{ $contact->mail_adm }}" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] ml-3 max-[1199px]:text-[14px]">
                            {{ $contact->mail_adm }}
                        </a>
                    </div>
                    @endif
            
                    @if($contact?->direction_sale)
                    <div class="flex items-start mb-5 sm:mb-[20px] w-[250px] max-[1199px]:w-full">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-1">
                            <g clip-path="url(#clip0_4003_5328)">
                                <path d="M10.0003 4.99999V9.99999L13.3337 11.6667M18.3337 9.99999C18.3337 14.6024 14.6027 18.3333 10.0003 18.3333C5.39795 18.3333 1.66699 14.6024 1.66699 9.99999C1.66699 5.39762 5.39795 1.66666 10.0003 1.66666C14.6027 1.66666 18.3337 5.39762 18.3337 9.99999Z" stroke="#BA2025" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_4003_5328">
                                    <rect width="20" height="20" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contact->direction_sale) }}" target="_blank" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] ml-3 max-[1199px]:text-[14px]">
                            {{ $contact->direction_sale }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-8 mt-[70px] max-[1199px]:order-1 max-[1199px]:mt-[40px]">
                <div class="bg-white">
                    <form wire:submit.prevent="submit" class="space-y-6 pt-[15px] max-[1199px]:space-y-4">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 max-[1199px]:grid-cols-1">
                            <div>
                                <label for="name" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] max-[1199px]:text-[14px]">Nombre y apellido*</label>
                                <input type="text" id="name" wire:model.blur="name" class="px-5 mt-3 sm:mt-[16px] w-full h-[50px] border border-[#E7E7E7] focus:border-transparent transition-all @error('name') border-red-500 @enderror max-[1199px]:mt-2">
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="company" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] max-[1199px]:text-[14px]">Empresa*</label>
                                <input type="text" id="company" wire:model.blur="company" class="px-5 mt-3 sm:mt-[16px] w-full h-[50px] border border-[#E7E7E7] focus:border-transparent transition-all @error('company') border-red-500 @enderror max-[1199px]:mt-2">
                                @error('company') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 max-[1199px]:grid-cols-1">
                            <div>
                                <label for="email" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] max-[1199px]:text-[14px]">E-mail*</label>
                                <input type="email" id="email" wire:model.blur="email" class="px-5 mt-3 sm:mt-[16px] w-full h-[50px] border border-[#E7E7E7] focus:border-transparent transition-all @error('email') border-red-500 @enderror max-[1199px]:mt-2">
                                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="phone" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] max-[1199px]:text-[14px]">Celular*</label>
                                <input type="text" id="phone" wire:model.blur="phone" class="px-5 mt-3 sm:mt-[16px] w-full h-[50px] border border-[#E7E7E7] focus:border-transparent transition-all @error('phone') border-red-500 @enderror max-[1199px]:mt-2">
                                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 max-[1199px]:grid-cols-1">

                            <div>
                                <label for="message" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] flex mb-4 sm:mb-[16px] max-[1199px]:text-[14px] max-[1199px]:mb-2">Mensaje*</label>
                                <textarea 
                                    id="message" 
                                    wire:model.blur="message" 
                                    rows="6" 
                                    class="px-5 py-3 w-full h-[140px] border border-[#E7E7E7] focus:border-transparent transition-all resize-none @error('message') border-red-500 @enderror"
                                ></textarea>
                                @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex flex-col justify-end sm:flex-row sm:items-end sm:justify-between gap-4 sm:gap-0 max-[1199px]:flex-col max-[1199px]:gap-4">
                                <div class="flex justify-between w-full max-[1199px]:flex-col max-[1199px]:gap-3">
                                    <p class="text-[#323232] font-montserrat text-[16px] font-normal leading-[150%] self-center max-[1199px]:text-[14px] max-[1199px]:self-start">*Datos obligatorios</p>

                                    <button 
                                        type="submit" 
                                        class="w-[184px] h-[44px] bg-transparent text-[#E40044] text-center font-inter text-[14px] rounded-[4px] border border-[#E40044] font-normal leading-normal uppercase">
                                        Enviar consulta
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
            </div>
            @if($contact?->frame_adm)
            <div class="mt-[98px] mb-[64px] sm:mt-[80px] max-w-[1224px] mx-auto max-[1199px]:mt-[60px]">
                <div class="w-full h-[574px] sm:h-[484px] relative max-[1199px]:h-[400px]">
                    {!! $contact->frame_adm !!}
                </div>
            </div>
            @endif

        
        <style>
            iframe {
                width: 100% !important;
                height: 100% !important;
                border: 0;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const iframe = document.querySelector('iframe');
    if (iframe) {
        iframe.style.pointerEvents = 'auto';
        }
        });
        </script>

</div>