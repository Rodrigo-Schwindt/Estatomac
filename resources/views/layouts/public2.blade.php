<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Estatomac | Fabricación y Mecanizado de Autopartes de Alta Precisión</title>

        <link rel="icon" href="/favicon.ico" sizes="16x16 32x32 48x48">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png" sizes="180x180">
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        
        <style>
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }
            
            .menu-open {
                animation: slideInRight 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }
            
            .menu-close {
                animation: slideOutRight 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }
            
            .backdrop-open {
                animation: fadeIn 0.3s ease-out forwards;
            }
            
            .backdrop-close {
                animation: fadeOut 0.2s ease-out forwards;
            }
            
            .menu-item {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .menu-item:hover {
                transform: translateX(8px);
                background-color: rgba(253, 39, 46, 0.1);
            }
            
            .hamburger-line {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .hamburger-open .line-1 {
                transform: rotate(45deg) translateY(8px);
            }
            
            .hamburger-open .line-2 {
                opacity: 0;
                transform: translateX(20px);
            }
            
            .hamburger-open .line-3 {
                transform: rotate(-45deg) translateY(-8px);
            }
            
            .nav-link {
                font-weight: 400;
                transition: all 0.2s ease;
            }

            .nav-link.active {
                font-weight: 700;
                color: dark;
            }
        </style>
    </head>
    <body class="bg-white text-[#1b1b18]">
        @php
            $contactData = App\Models\Contact::first();
            $hasSocialMedia = $contactData && ($contactData->facebook || $contactData->insta || $contactData->linkedin || $contactData->youtube);
        @endphp

        <header 
            x-data="{ open: false }"
            class="items-center bg-white items-center flex z-50 w-full h-[100px]">

            <div class="max-w-[1224px] mx-auto flex justify-between items-center w-full px-4 lg:px-0">

                @if($contactData && $contactData->icono_1)
                <a wire:navigate href="{{ url('/') }}" class="w-fit h-[58px] flex-shrink-0 cursor-pointer max-lg:w-40">
                    <img src="{{ Storage::url($contactData->icono_1) }}" class="w-full h-full object-contain">
                </a>
                @endif

                <nav class="hidden lg:flex items-center">
                    <div class="flex h-[40px] py-2 gap-[20px] items-center">
                
                        <a wire:navigate href="/nosotros"
                           class="nav-link {{ request()->is('nosotros') ? 'active' : '' }} text-black font-inter text-[14px] font-normal leading-normal relative">
                           NOSOTROS
                        </a>
                
                        <a wire:navigate href="/productos"
                           class="nav-link {{ request()->is('productos*') ? 'active' : '' }} text-black font-inter text-[14px] font-normal leading-normal relative">
                           PRODUCTOS
                        </a>
                
                        <a wire:navigate href="/novedades"
                            class="nav-link {{ request()->is('novedades') ? 'active' : '' }} text-black font-inter text-[14px] font-normal leading-normal relative">
                            NOVEDADES
                        </a>
                        
                        <a wire:navigate href="/contacto"
                            class="nav-link {{ request()->is('contacto') ? 'active' : '' }} text-black font-inter text-[14px] font-normal leading-normal relative">
                            CONTACTO
                        </a>

                        @auth('cliente')
<a  href="{{ route('cliente.productos') }}"
   class="nav-link text-black cursor-pointer font-inter text-[14px] font-normal
          w-[164px] h-[44px] rounded-[4px] border border-black
          flex justify-center items-center text-center relative
          hover:bg-black hover:text-white transition-colors">
    {{ Auth::guard('cliente')->user()->nombre }}
</a>
@else
<livewire:auth.login-modal />
@endauth
                     
                        <div class="h-full flex justify-center items-center text-center relative ml-[8px]">
                            <svg width="1" height="34" viewBox="0 0 1 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.349976 0L0.349977 34" stroke="black" stroke-width="0.7"/>
                            </svg>
                        </div>
                
                        <div>
                            <livewire:search-modal2 />
                        </div>
                    </div>
                </nav>

                <div class="lg:hidden ml-[60px]">
                    <livewire:search-modal2 />
                </div>

                <button id="hamburger-btn"
                    @click="open = true"
                    class="lg:hidden flex flex-col justify-center items-end gap-[6px] w-[38px] h-[38px] z-[60]">
                    <span class="hamburger-line block w-8 h-[3px] bg-[#BA2025] rounded"></span>
                    <span class="hamburger-line block w-6 h-[3px] bg-[#BA2025] rounded"></span>
                    <span class="hamburger-line block w-8 h-[3px] bg-[#BA2025] rounded"></span>
                </button>
            </div>

            <div
                x-show="open"
                x-transition.opacity
                @click="open = false"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[98] lg:hidden">
            </div>

            <aside 
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0 opacity-100"
                x-transition:leave-end="translate-x-full opacity-0"
                class="fixed top-0 right-0 h-full w-[85%] max-w-[380px] bg-white z-[99] shadow-2xl overflow-y-auto lg:hidden">

                <div class="bg-gradient-to-r from-[#BA2025] to-[#8B1419] px-6 py-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-white font-montserrat text-[24px] font-bold">Menú</h2>
                            <p class="text-white/80 font-montserrat text-[14px] mt-1">Navegación</p>
                        </div>

                        <button 
                            @click="open = false" 
                            class="text-white hover:bg-white/10 p-2 rounded-lg transition-colors"
                            aria-label="Cerrar menú"
                        >
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <nav class="px-6 py-6 space-y-2">
                    <a wire:navigate 
                       href="/nosotros"
                       @click="open = false"
                       class="group flex items-center gap-3 px-4 py-3 rounded-lg transition-all
                              {{ request()->is('nosotros') 
                                  ? 'bg-[#BA2025] text-white shadow-md' 
                                  : 'text-gray-700 hover:bg-gray-50 hover:text-[#BA2025]' }}">
                        <svg class="w-5 h-5 {{ request()->is('nosotros') ? '' : 'text-gray-400 group-hover:text-[#BA2025]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="font-montserrat text-[16px] font-medium">Nosotros</span>
                    </a>

                    <a wire:navigate 
                       href="/productos"
                       @click="open = false"
                       class="group flex items-center gap-3 px-4 py-3 rounded-lg transition-all
                              {{ request()->is('productos*') 
                                  ? 'bg-[#BA2025] text-white shadow-md' 
                                  : 'text-gray-700 hover:bg-gray-50 hover:text-[#BA2025]' }}">
                        <svg class="w-5 h-5 {{ request()->is('productos*') ? '' : 'text-gray-400 group-hover:text-[#BA2025]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span class="font-montserrat text-[16px] font-medium">Productos</span>
                    </a>

                    <a wire:navigate 
                       href="/novedades"
                       @click="open = false"
                       class="group flex items-center gap-3 px-4 py-3 rounded-lg transition-all
                              {{ request()->is('novedades*') 
                                  ? 'bg-[#BA2025] text-white shadow-md' 
                                  : 'text-gray-700 hover:bg-gray-50 hover:text-[#BA2025]' }}">
                        <svg class="w-5 h-5 {{ request()->is('novedades*') ? '' : 'text-gray-400 group-hover:text-[#BA2025]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        <span class="font-montserrat text-[16px] font-medium">Novedades</span>
                    </a>

                    <a wire:navigate 
                       href="/contacto"
                       @click="open = false"
                       class="group flex items-center gap-3 px-4 py-3 rounded-lg transition-all
                              {{ request()->is('contacto*') 
                                  ? 'bg-[#BA2025] text-white shadow-md' 
                                  : 'text-gray-700 hover:bg-gray-50 hover:text-[#BA2025]' }}">
                        <svg class="w-5 h-5 {{ request()->is('contacto*') ? '' : 'text-gray-400 group-hover:text-[#BA2025]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-montserrat text-[16px] font-medium">Contacto</span>
                    </a>
                </nav>

                <div class="px-6 pb-6">
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-gray-900 font-montserrat text-[14px] font-bold mb-4 uppercase tracking-wide">Contacto</h3>
                        
                        <div class="space-y-3">
                            @if($contactData && $contactData->phone_amd)
                            <a href="tel:{{ $contactData->phone_amd }}" 
                               class="flex items-center gap-3 text-gray-600 hover:text-[#BA2025] transition-colors group">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 group-hover:bg-[#BA2025]/10 flex items-center justify-center transition-colors">
                                    <svg class="w-5 h-5 text-gray-500 group-hover:text-[#BA2025]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <span class="text-[15px] font-montserrat font-medium">{{ $contactData->phone_amd }}</span>
                            </a>
                            @endif

                            @if($contactData && $contactData->mail_adm)
                            <a href="mailto:{{ $contactData->mail_adm }}" 
                               class="flex items-center gap-3 text-gray-600 hover:text-[#BA2025] transition-colors group">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 group-hover:bg-[#BA2025]/10 flex items-center justify-center transition-colors">
                                    <svg class="w-5 h-5 text-gray-500 group-hover:text-[#BA2025]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="text-[15px] font-montserrat font-medium break-all">{{ $contactData->mail_adm }}</span>
                            </a>
                            @endif
                        </div>

                        @if($hasSocialMedia)
                        <div class="mt-6">
                            <h3 class="text-gray-900 font-montserrat text-[14px] font-bold mb-4 uppercase tracking-wide">Síguenos</h3>
                            <div class="flex items-center gap-3">
                                @if($contactData->facebook)
                                <a href="{{ $contactData->facebook }}" 
                                   target="_blank"
                                   class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-[#BA2025] flex items-center justify-center transition-all group">
                                    <svg class="w-5 h-5 text-gray-600 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                                @endif
                                @if($contactData->insta)
                                <a href="{{ $contactData->insta }}" 
                                   target="_blank"
                                   class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-[#BA2025] flex items-center justify-center transition-all group">
                                    <svg class="w-5 h-5 text-gray-600 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                </a>
                                @endif
                                @if($contactData->linkedin)
                                <a href="{{ $contactData->linkedin }}" 
                                   target="_blank"
                                   class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-[#BA2025] flex items-center justify-center transition-all group">
                                    <svg class="w-5 h-5 text-gray-600 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                </a>
                                @endif
                                @if($contactData->youtube)
                                <a href="{{ $contactData->youtube }}" 
                                   target="_blank"
                                   class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-[#BA2025] flex items-center justify-center transition-all group">
                                    <svg class="w-5 h-5 text-gray-600 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </aside>

        </header>

        <main class="min-h-screen">
            {{ $slot }}
        </main>

        @if($contactData && $contactData->wssp)
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactData->wssp) }}" 
           target="_blank"
           style="position: fixed; bottom: 24px; right: 24px; z-index: 9999;"
           title="Contactar por WhatsApp">
            <div style="position: relative; width: 64px; height: 64px;">
                <div style="width: 64px; height: 64px; background-color: #25D366; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4); cursor: pointer; transition: all 0.3s ease;">
                    <svg style="width: 36px; height: 36px; color: white;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                </div>
                
                <div style="position: absolute; top: 0; left: 0; width: 64px; height: 64px; background-color: #25D366; border-radius: 50%; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; opacity: 0.2; pointer-events: none;"></div>
            </div>
        </a>

        <style>
            @keyframes pulse {
                0%, 100% {
                    opacity: 0.2;
                    transform: scale(1);
                }
                50% {
                    opacity: 0;
                    transform: scale(1.5);
                }
            }
            
            a[title="Contactar por WhatsApp"]:hover div > div {
                transform: scale(1.1);
                box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
            }
        </style>
        @endif

        @livewire('footer') 

        <div 
            x-data="{ show: false, message: '', type: '' }"
            @show-toast.window="
                message = $event.detail.message;
                type = $event.detail.type ?? 'success';
                show = true;
                setTimeout(() => show = false, 4000);
            "
            x-show="show"
            x-transition
            class="fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white z-[9999]"
            :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'"
        >
            <span x-text="message"></span>
        </div>

        {{-- Script para abrir modal de login automáticamente --}}
        @if(session('openLoginModal'))
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.dispatch('open-login');
            });
        </script>
        @endif

        <script>
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    Livewire.dispatch('close-search');
                    closeMobileMenu();
                }
            });

            function toggleMobileMenu() {
                const menu = document.getElementById('mobile-menu');
                const backdrop = document.getElementById('menu-backdrop');
                const hamburger = document.getElementById('hamburger-btn');
                
                const isHidden = menu.classList.contains('hidden');
                
                if (isHidden) {
                    menu.classList.remove('hidden');
                    backdrop.classList.remove('hidden');
                    
                    void menu.offsetWidth;
                    void backdrop.offsetWidth;
                    
                    menu.classList.add('menu-open');
                    backdrop.classList.add('backdrop-open');
                    hamburger.classList.add('hamburger-open');
                    document.body.style.overflow = 'hidden';
                } else {
                    closeMobileMenu();
                }
            }

            function closeMobileMenu() {
                const menu = document.getElementById('mobile-menu');
                const backdrop = document.getElementById('menu-backdrop');
                const hamburger = document.getElementById('hamburger-btn');
                
                if (!menu.classList.contains('hidden')) {
                    menu.classList.remove('menu-open');
                    menu.classList.add('menu-close');
                    backdrop.classList.remove('backdrop-open');
                    backdrop.classList.add('backdrop-close');
                    hamburger.classList.remove('hamburger-open');
                    
                    setTimeout(() => {
                        menu.classList.add('hidden');
                        backdrop.classList.add('hidden');
                        menu.classList.remove('menu-close');
                        backdrop.classList.remove('backdrop-close');
                        document.body.style.overflow = 'auto';
                    }, 300);
                }
            }

            document.getElementById('menu-backdrop')?.addEventListener('click', closeMobileMenu);

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    closeMobileMenu();
                }
            });

            document.getElementById('mobile-menu')?.addEventListener('wheel', function(e) {
                e.stopPropagation();
            }, { passive: true });
        </script>
    </body>
</html>