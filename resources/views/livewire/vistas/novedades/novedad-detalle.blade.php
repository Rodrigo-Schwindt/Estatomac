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

         <div class="bg-[#F5F5F5]  h-[150px]">
        <div class="max-w-[1224px] mx-auto">
                <div class=" max-[1199px]:px-4 ">
            <div class="max-w-[1224px] mx-auto pt-[16px]">
                <nav class="text-white font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] flex items-center gap-1">
        <a wire:navigate href="{{ url('/') }}" class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] font-bold leading-[150%] transition-colors duration-200">Inicio</a>
        <span class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%]">|</span>
        <a wire:navigate href="/novedades" class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] font-bold leading-[150%] transition-colors duration-200">Novedades</a>
        <span class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%]">|</span>
        <span class="text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] line-clamp-1">{{ $novedad->title }}</span>
                </nav>
            </div>
        </div>
        <h1 class="text-[40px] pt-[43px] font-bold">Novedades</h1>
        </div>
    </div>
  

    <div class="max-w-[1224px] mx-auto flex justify-center max-[1199px]:px-4">
        
        <div class="mt-[50px] max-[1199px]:mt-10 max-[639px]:mt-8 w-full">
            @if($showDetail)
                <div class="w-full md:w-[900px] flex flex-col max-[1199px]:w-full mx-auto">
                    <div class="max-w-[900px] max-h-[450px] mb-[40px] flex justify-center max-[1199px]:mb-[24px] max-[1199px]:max-w-full max-[1199px]:max-h-[350px] max-[767px]:max-h-[300px] max-[639px]:max-h-[250px] max-[639px]:mb-5 overflow-hidden rounded-[4px]">
                        <img 
                            src="{{ Storage::url($novedad->image) }}" 
                            alt="{{ $novedad->title }}"
                            class="w-full max-h-[422px] object-cover max-[1199px]:max-h-[350px] max-[767px]:max-h-[300px] max-[639px]:max-h-[250px] transition-transform duration-500"
                        >
                    </div>

                    <p class="text-[#23378C] font-Inter text-[18px] font-normal leading-[120%] max-[1199px]:text-[16px] max-[767px]:text-[15px] max-[639px]:text-[14px] animate-fadeIn">
                        {{ $novedad->novcategories->first()->title ?? 'Novedad' }}
                    </p>

                    <h1 class="text-black font-Inter text-[34px] mt-[8px] font-bold leading-[120%] w-[724px] mb-[24px] max-[1199px]:w-full max-[1199px]:text-[28px] max-[767px]:text-[24px] max-[639px]:text-[22px] max-[1199px]:mb-[16px] max-[639px]:mb-4 max-[639px]:mt-2 animate-fadeIn">
                        {{ $novedad->title }}
                    </h1>

                    <div class="text-[#111] font-Inter text-[18px] font-normal leading-[150%] w-[786px] content-description max-[1199px]:w-full max-[1199px]:text-[16px] max-[767px]:text-[15px] max-[639px]:text-[14px] max-[639px]:leading-[160%] animate-fadeIn">
                        {!! str_replace('&nbsp;', ' ', $novedad->description) !!}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
        }
        
        .animate-fadeIn:nth-child(1) {
            animation-delay: 0.1s;
        }
        
        .animate-fadeIn:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .animate-fadeIn:nth-child(3) {
            animation-delay: 0.3s;
        }

        .content-description p {
            margin-bottom: 1rem;
        }

        .content-description h2,
        .content-description h3 {
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .content-description ul,
        .content-description ol {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .content-description a {
            color: #23378C;
            text-decoration: underline;
            transition: opacity 0.2s;
        }

        .content-description img {
            max-width: 100%;
            height: auto;
            margin: 1.5rem 0;
            border-radius: 4px;
        }

        @media (max-width: 1199px) {
            .content-description p {
                margin-bottom: 0.875rem;
            }

            .content-description h2,
            .content-description h3 {
                margin-top: 1.25rem;
                margin-bottom: 0.625rem;
            }

            .content-description ul,
            .content-description ol {
                margin-left: 1.25rem;
                margin-bottom: 0.875rem;
            }

            .content-description img {
                margin: 1.25rem 0;
            }
        }

        @media (max-width: 639px) {
            .content-description p {
                margin-bottom: 0.75rem;
            }

            .content-description h2,
            .content-description h3 {
                margin-top: 1rem;
                margin-bottom: 0.5rem;
                font-size: 1.125rem;
            }

            .content-description ul,
            .content-description ol {
                margin-left: 1rem;
                margin-bottom: 0.75rem;
            }

            .content-description img {
                margin: 1rem 0;
            }
        }
    </style>
</div>