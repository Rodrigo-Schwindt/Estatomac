<div
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, 50)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 transform -translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0">

        <div class="bg-[#F5F5F5]  h-[150px]">
        <div class="max-w-[1224px] mx-auto">
                <div class=" max-[1199px]:px-4 ">
            <div class="max-w-[1224px] mx-auto pt-[16px]">
                <nav class="text-white font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] flex items-center gap-1">
                    <a wire:navigate href="{{ url('/') }}" class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] font-bold leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Inicio</a>
                    <span class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">|</span>
                    <span class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Contacto</span>
                </nav>
            </div>
        </div>
        <h1 class="text-[40px] pt-[43px] font-bold lg:px-0 px-4">Contacto</h1>
        </div>
    </div>

    <div>
        @if($successMessage)
        <div class="max-w-[1224px] mx-auto mt-6 mb-6 sm:mb-8 bg-green-50 border border-green-200 text-green-800 px-4 sm:px-6 py-4 rounded-lg flex items-center animate-fade-in max-[1199px]:mx-4" id="success-message">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="max-[639px]:text-[14px]">{{ $successMessage }}</span>
        </div>
        @endif

        @if($errorMessage)
        <div class="max-w-[1224px] mx-auto mb-6 mt-6 sm:mb-8 bg-red-50 border border-red-200 text-red-800 px-4 sm:px-6 py-4 rounded-lg flex items-center animate-fade-in max-[1199px]:mx-4">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="max-[639px]:text-[14px]">{{ $errorMessage }}</span>
        </div>
        @endif

        <div class="max-w-[1224px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-[24px] max-[1199px]:flex max-[1199px]:flex-col max-[1199px]:px-4 max-[1199px]:gap-8">

            <div class="lg:col-span-4 max-[1199px]:order-2">
                <div class="max-[1199px]:w-full mt-[84px] max-[1199px]:mt-[40px] max-[639px]:mt-8 animate-fadeInLeft">
                    @if($contact?->direction_adm)
                    <div class="flex items-start mb-5 sm:mb-[20px] w-[345px] max-[1199px]:w-full max-[639px]:mb-4 transition-transform duration-200">
<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" class="mt-1" viewBox="0 0 20 20" fill="none">
  <path d="M16.6668 8.33329C16.6668 13.3333 10.0002 18.3333 10.0002 18.3333C10.0002 18.3333 3.3335 13.3333 3.3335 8.33329C3.3335 6.56518 4.03588 4.86949 5.28612 3.61925C6.53636 2.36901 8.23205 1.66663 10.0002 1.66663C11.7683 1.66663 13.464 2.36901 14.7142 3.61925C15.9645 4.86949 16.6668 6.56518 16.6668 8.33329Z" stroke="#018637" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M10 10.8334C11.3807 10.8334 12.5 9.71409 12.5 8.33337C12.5 6.95266 11.3807 5.83337 10 5.83337C8.61929 5.83337 7.5 6.95266 7.5 8.33337C7.5 9.71409 8.61929 10.8334 10 10.8334Z" stroke="#018637" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contact->direction_adm) }}" target="_blank" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] ml-3 max-[1199px]:text-[15px] max-[639px]:text-[14px] max-[639px]:ml-2">
                            {{ $contact->direction_adm }}
                        </a>
                    </div>
                    @endif
            
                    
                    @if($contact?->phone_amd)
                    <div class="flex items-center mb-5 sm:mb-[20px] max-[639px]:mb-4 transition-transform duration-200">
<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
  <g clip-path="url(#clip0_2437_1831)">
    <path d="M18.3332 14.1V16.6C18.3341 16.8321 18.2866 17.0618 18.1936 17.2745C18.1006 17.4871 17.9643 17.678 17.7933 17.8349C17.6222 17.9918 17.4203 18.1113 17.2005 18.1856C16.9806 18.26 16.7477 18.2876 16.5165 18.2667C13.9522 17.9881 11.489 17.1118 9.32486 15.7084C7.31139 14.4289 5.60431 12.7219 4.32486 10.7084C2.91651 8.53438 2.04007 6.0592 1.76653 3.48337C1.7457 3.25293 1.77309 3.02067 1.84695 2.80139C1.9208 2.58211 2.03951 2.38061 2.1955 2.20972C2.3515 2.03883 2.54137 1.9023 2.75302 1.80881C2.96468 1.71532 3.19348 1.66692 3.42486 1.66671H5.92486C6.32928 1.66273 6.72136 1.80594 7.028 2.06965C7.33464 2.33336 7.53493 2.69958 7.59153 3.10004C7.69705 3.9001 7.89274 4.68565 8.17486 5.44171C8.28698 5.73998 8.31125 6.06414 8.24478 6.37577C8.17832 6.68741 8.02392 6.97347 7.79986 7.20004L6.74153 8.25837C7.92783 10.3447 9.65524 12.0721 11.7415 13.2584L12.7999 12.2C13.0264 11.976 13.3125 11.8216 13.6241 11.7551C13.9358 11.6887 14.2599 11.7129 14.5582 11.825C15.3143 12.1072 16.0998 12.3029 16.8999 12.4084C17.3047 12.4655 17.6744 12.6694 17.9386 12.9813C18.2029 13.2932 18.3433 13.6914 18.3332 14.1Z" stroke="#018637" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  </g>
  <defs>
    <clipPath id="clip0_2437_1831">
      <rect width="20" height="20" fill="white"/>
    </clipPath>
  </defs>
</svg>
                        <a href="tel:{{ $contact->phone_amd }}" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] ml-3 max-[1199px]:text-[15px] max-[639px]:text-[14px] max-[639px]:ml-2">
                            {{ $contact->phone_amd }}
                        </a>
                    </div>
                    @endif
                    
                    @if($contact?->mail_adm)
                    <div class="flex items-center mb-5 sm:mb-[20px] max-[639px]:mb-4 transition-transform duration-200">
<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
  <path d="M16.6665 3.33337H3.33317C2.4127 3.33337 1.6665 4.07957 1.6665 5.00004V15C1.6665 15.9205 2.4127 16.6667 3.33317 16.6667H16.6665C17.587 16.6667 18.3332 15.9205 18.3332 15V5.00004C18.3332 4.07957 17.587 3.33337 16.6665 3.33337Z" stroke="#018637" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M18.3332 5.83337L10.8582 10.5834C10.6009 10.7446 10.3034 10.83 9.99984 10.83C9.69624 10.83 9.39878 10.7446 9.1415 10.5834L1.6665 5.83337" stroke="#018637" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                        <a href="mailto:{{ $contact->mail_adm }}" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] ml-3 max-[1199px]:text-[15px] max-[639px]:text-[14px] max-[639px]:ml-2 break-all">
                            {{ $contact->mail_adm }}
                        </a>
                    </div>
                    @endif
            
                    @if($contact?->direction_sale)
                    <div class="flex items-start mb-5 sm:mb-[20px] w-[250px] max-[1199px]:w-full max-[639px]:mb-4 transition-transform duration-200">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 mt-1 max-[639px]:w-5 max-[639px]:h-5">
                            <g clip-path="url(#clip0_4003_5328)">
                                <path d="M10.0003 4.99999V9.99999L13.3337 11.6667M18.3337 9.99999C18.3337 14.6024 14.6027 18.3333 10.0003 18.3333C5.39795 18.3333 1.66699 14.6024 1.66699 9.99999C1.66699 5.39762 5.39795 1.66666 10.0003 1.66666C14.6027 1.66666 18.3337 5.39762 18.3337 9.99999Z" stroke="#BA2025" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_4003_5328">
                                    <rect width="20" height="20" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contact->direction_sale) }}" target="_blank" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] ml-3 max-[1199px]:text-[15px] max-[639px]:text-[14px] max-[639px]:ml-2">
                            {{ $contact->direction_sale }}
                        </a>
                    </div>
                    @endif
                                        @if($contact?->wssp)
                    <div class="flex items-start mb-5 sm:mb-[20px] w-[318px] max-[1199px]:w-full max-[639px]:mb-4 transition-transform duration-200">
<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
  <path fill-rule="evenodd" clip-rule="evenodd" d="M14.5823 11.985C14.3328 11.8608 13.1095 11.2625 12.8817 11.1792C12.6539 11.0967 12.4881 11.0558 12.3215 11.3042C12.1557 11.5508 11.6793 12.1092 11.5344 12.2742C11.3887 12.44 11.2438 12.46 10.9952 12.3367C10.7465 12.2117 9.94429 11.9508 8.99391 11.1075C8.25453 10.4509 7.75464 9.64002 7.60978 9.39169C7.46492 9.14419 7.59387 9.01002 7.71863 8.88669C7.83084 8.77585 7.96732 8.59752 8.09209 8.45335C8.21685 8.30835 8.25788 8.20502 8.34078 8.03919C8.42451 7.87419 8.38265 7.73002 8.31985 7.60585C8.25788 7.48169 7.7605 6.26252 7.55284 5.76669C7.35104 5.28419 7.14589 5.35003 6.99349 5.34169C6.8478 5.33503 6.682 5.33336 6.51621 5.33336C6.35041 5.33336 6.08079 5.39503 5.85303 5.64336C5.62444 5.89086 4.98219 6.49002 4.98219 7.70919C4.98219 8.92752 5.87313 10.105 5.99789 10.2709C6.12266 10.4359 7.75213 12.9375 10.2482 14.01C10.8428 14.265 11.3058 14.4175 11.6667 14.5308C12.2629 14.72 12.8055 14.6933 13.2342 14.6292C13.7115 14.5583 14.7063 14.03 14.9139 13.4517C15.1208 12.8733 15.1208 12.3775 15.0588 12.2742C14.9968 12.1708 14.831 12.1092 14.5815 11.985H14.5823ZM10.0423 18.1542H10.0389C8.55634 18.1544 7.10099 17.7578 5.8254 17.0058L5.52396 16.8275L2.39062 17.6458L3.22712 14.6058L3.03035 14.2942C2.20149 12.9811 1.76286 11.4615 1.76512 9.91085C1.7668 5.36919 5.47958 1.6742 10.0456 1.6742C12.2562 1.6742 14.3345 2.53253 15.897 4.08919C16.6676 4.85301 17.2785 5.76133 17.6941 6.7616C18.1098 7.76188 18.322 8.83425 18.3186 9.91668C18.3169 14.4583 14.6041 18.1542 10.0423 18.1542ZM17.086 2.9067C16.1634 1.98247 15.0657 1.24965 13.8564 0.7507C12.6472 0.251754 11.3505 -0.00339687 10.0414 3.41479e-05C4.55347 3.41479e-05 0.0854091 4.44586 0.0837344 9.91002C0.0811914 11.649 0.539563 13.3578 1.4126 14.8642L0 20L5.27861 18.6217C6.73884 19.4134 8.37519 19.8283 10.0381 19.8283H10.0423C15.5302 19.8283 19.9983 15.3825 20 9.91752C20.004 8.61525 19.7485 7.32511 19.2484 6.12172C18.7482 4.91833 18.0132 3.82559 17.086 2.9067Z" fill="#018637"/>
</svg>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->wssp) }}" target="_blank" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] ml-3 max-[1199px]:text-[15px] max-[639px]:text-[14px] max-[639px]:ml-2">
                            {{ $contact->wssp }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-8 mt-[70px] max-[1199px]:order-1 max-[1199px]:mt-[40px] max-[639px]:mt-8">
                <div class="bg-white animate-fadeInRight">
                    <form wire:submit.prevent="submit" class="space-y-6 pt-[15px] max-[1199px]:space-y-4 max-[639px]:space-y-3 max-[639px]:pt-2">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 max-[1199px]:grid-cols-1 max-[1199px]:gap-4">
                            <div class="transition-all duration-200">
                                <label for="name" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] max-[1199px]:text-[15px] max-[639px]:text-[14px]">Nombre y apellido*</label>
                                <input type="text" id="name" wire:model.blur="name" class="px-5 mt-3 sm:mt-[16px] w-full h-[50px] max-[639px]:h-[44px] rounded-[4px] border border-[#EAEAEA] focus:border-[#E40044] focus:outline-none focus:ring-2 focus:ring-[#E40044]/20 transition-all duration-200 @error('name') border-red-500 @enderror max-[1199px]:mt-2 max-[639px]:px-4 max-[639px]:text-[14px]">
                                @error('name') <p class="mt-1 text-sm max-[639px]:text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="transition-all duration-200">
                                <label for="company" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] max-[1199px]:text-[15px] max-[639px]:text-[14px]">Empresa*</label>
                                <input type="text" id="company" wire:model.blur="company" class="px-5 mt-3 sm:mt-[16px] w-full h-[50px] rounded-[4px] max-[639px]:h-[44px] border border-[#EAEAEA] focus:border-[#E40044] focus:outline-none focus:ring-2 focus:ring-[#E40044]/20 transition-all duration-200 @error('company') border-red-500 @enderror max-[1199px]:mt-2 max-[639px]:px-4 max-[639px]:text-[14px]">
                                @error('company') <p class="mt-1 text-sm max-[639px]:text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 max-[1199px]:grid-cols-1 max-[1199px]:gap-4">
                            <div class="transition-all duration-200">
                                <label for="email" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] max-[1199px]:text-[15px] max-[639px]:text-[14px]">E-mail*</label>
                                <input type="email" id="email" wire:model.blur="email" class="px-5 mt-3 sm:mt-[16px] w-full h-[50px] rounded-[4px] max-[639px]:h-[44px] border border-[#EAEAEA] focus:border-[#E40044] focus:outline-none focus:ring-2 focus:ring-[#E40044]/20 transition-all duration-200 @error('email') border-red-500 @enderror max-[1199px]:mt-2 max-[639px]:px-4 max-[639px]:text-[14px]">
                                @error('email') <p class="mt-1 text-sm max-[639px]:text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="transition-all duration-200">
                                <label for="phone" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] max-[1199px]:text-[15px] max-[639px]:text-[14px]">Celular*</label>
                                <input type="text" id="phone" wire:model.blur="phone" class="px-5 mt-3 sm:mt-[16px] w-full h-[50px] rounded-[4px] max-[639px]:h-[44px] border border-[#EAEAEA] focus:border-[#E40044] focus:outline-none focus:ring-2 focus:ring-[#E40044]/20 transition-all duration-200 @error('phone') border-red-500 @enderror max-[1199px]:mt-2 max-[639px]:px-4 max-[639px]:text-[14px]">
                                @error('phone') <p class="mt-1 text-sm max-[639px]:text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex flex-col gap-4 sm:gap-6">

                            <div class="transition-all duration-200">
                                <label for="message" class="text-[#111010] font-inter text-[16px] font-normal leading-normal tracking-[-0.01em] flex mb-4 sm:mb-[16px] max-[639px]:text-[14px] max-[639px]:mb-2">Mensaje*</label>
                                <textarea
                                    id="message"
                                    wire:model.blur="message"
                                    rows="6"
                                    class="px-5 py-3 w-full h-[140px] max-[639px]:h-[120px] rounded-[4px] border border-[#EAEAEA] focus:border-[#E40044] focus:outline-none focus:ring-2 focus:ring-[#E40044]/20 transition-all duration-200 resize-none @error('message') border-red-500 @enderror max-[639px]:px-4 max-[639px]:py-2 max-[639px]:text-[14px]"
                                ></textarea>
                                @error('message') <p class="mt-1 text-sm max-[639px]:text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex items-center justify-between gap-4 max-[639px]:flex-col max-[639px]:items-start max-[639px]:gap-3">
                                <p class="text-[#323232] font-montserrat text-[16px] font-normal leading-[150%] max-[639px]:text-[13px]">*Campos obligatorios</p>

                                <button
                                    type="submit"
                                    class="w-[170px] max-[639px]:w-full h-[40px] max-[639px]:h-[40px] bg-[#23378C] text-[#fff] text-center font-inter text-[16px] max-[639px]:text-[13px] cursor-pointer rounded-[40px] border border-[#23378C] font-normal leading-normal  transition-all duration-300 hover:bg-[#E4002B] hover:border-[#E4002B] hover:text-white">
                                    Enviar consulta
                                </button>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>

        @if($contact?->frame_adm)
        <div class="mt-[98px] mb-[64px] sm:mt-[80px] max-w-[1224px] mx-auto max-[1199px]:mt-[60px] max-[1199px]:px-4 max-[639px]:mt-12 max-[639px]:mb-12">
            <div class="w-full h-[574px] sm:h-[484px] relative max-[1199px]:h-[400px] max-[767px]:h-[350px] max-[639px]:h-[300px] rounded-lg overflow-hidden animate-fadeIn">
                {!! $contact->frame_adm !!}
            </div>
        </div>
        @endif
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .animate-fadeInLeft {
            animation: fadeInLeft 0.6s ease-out forwards;
        }

        .animate-fadeInRight {
            animation: fadeInRight 0.6s ease-out forwards;
        }

        iframe {
            width: 100% !important;
            height: 100% !important;
            border: 0;
            filter: grayscale(100%);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const iframe = document.querySelector('iframe');
            if (iframe) {
                iframe.style.pointerEvents = 'auto';
                let src = iframe.getAttribute('src') || '';
                src = src.replace(/maptype=satellite/gi, 'maptype=roadmap');
                if (!src.includes('maptype=')) {
                    src += (src.includes('?') ? '&' : '?') + 'maptype=roadmap';
                }
                iframe.setAttribute('src', src);
            }
        });
    </script>

</div>