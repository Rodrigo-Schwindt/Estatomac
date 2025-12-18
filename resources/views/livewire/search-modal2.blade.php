<div x-data 
     @click.outside="$wire.isOpen && $wire.toggleSearch()">
    <button 
        wire:click="toggleSearch" 
        class="flex items-center justify-center rounded-lg transition-all duration-200 group cursor-pointer p-2 max-lg:hover:bg-white/10"
        aria-label="Abrir búsqueda">
        <svg class="w-6 h-6 text-[#222] group-hover:text-[#BA2025] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </button>

    <div 
        class="hidden lg:block fixed top-[90px] left-0 right-0 bg-white shadow-lg transform transition-all duration-300 ease-in-out z-[60] border-b border-gray-200"
        style="
            max-height: {{ $isOpen ? '80vh' : '0' }};
            overflow-y: {{ $isOpen ? 'auto' : 'hidden' }};
            overflow-x: hidden;
            opacity: {{ $isOpen ? '1' : '0' }};
        "
    >
        <div class="max-w-[1366px] mx-auto px-4 py-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="flex-1 relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input 
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Buscar productos, servicios..."
                        class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-lg text-[#1b1b18] placeholder-gray-400 focus:outline-none focus:border-[#BA2025] transition-all"
                        x-ref="searchInputDesktop">
                </div>
                <button 
                    wire:click="toggleSearch"
                    class="p-3 cursor-pointer rounded-lg transition-colors group"
                    aria-label="Cerrar búsqueda">
                    <svg class="w-6 h-6 text-gray-600 group-hover:text-[#BA2025]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="max-h-[60vh] overflow-y-auto">
                @if($search && strlen($search) >= 2)
                    @if($isSearching)
                        <div class="py-12 text-center">
                            <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-gray-200 border-t-[#BA2025]"></div>
                            <p class="mt-4 text-gray-600">Buscando productos...</p>
                        </div>
                    @elseif($results->isEmpty())
                        <div class="py-12 text-center">
                            <svg class="mx-auto h-20 w-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-xl font-bold text-[#1b1b18] mb-2">No se encontraron productos</h3>
                            <p class="text-gray-600">Intenta con otros términos de búsqueda</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pb-6">
                            @foreach($results as $product)
<a 
href="{{ route('productos.show', $product) }}"
    wire:navigate
    wire:click="toggleSearch"
    class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg hover:border-[#BA2025] hover:shadow-md transition-all group bg-white">
    
    @if($product->images->first())
    <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg overflow-hidden">
        <img 
            src="{{ Storage::url($product->images->first()->image) }}" 
            alt="{{ $product->title }}"
            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
    </div>
    @else
    <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
    </div>
    @endif

    <div class="flex-1 min-w-0">
        <h4 class="font-bold text-[#1b1b18] group-hover:text-[#BA2025] transition-colors mb-1 line-clamp-2">
            {{ $product->title }}
        </h4>
        
        @if($product->ficha_tecnica)
        <p class="text-sm text-gray-600 line-clamp-2 mb-2">
            {{ Str::limit(strip_tags($product->ficha_tecnica), 80) }}
        </p>
        @endif

        @if($product->category)
        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
            {{ $product->category->title }}
        </span>
        @endif

        @if($product->destacado)
        <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded text-xs font-bold bg-[#BA2025] text-white">
            ★ Destacado
        </span>
        @endif
    </div>

    <svg class="w-5 h-5 text-gray-400 group-hover:text-[#BA2025] group-hover:translate-x-1 transition-all flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
</a>
@endforeach
                        </div>

                        <div class="py-4 border-t border-gray-200 bg-gray-50 -mx-4 px-4">
                            <p class="text-sm text-gray-600 text-center">
                                Se encontraron <span class="font-bold text-[#BA2025]">{{ $results->count() }}</span> producto(s)
                            </p>
                        </div>
                    @endif
                @else

                @endif
            </div>
        </div>
    </div>

    <!-- Search Mobile (Modal Full Screen) -->
    @if($isOpen)
    <div class="lg:hidden fixed inset-0 z-[70] bg-white animate-fadeIn">
        <!-- Header del modal -->
        <div class="sticky top-0 bg-[#1B1A17] border-b border-white/10 p-4 shadow-lg z-10">
            <div class="flex items-center gap-3">
                <button 
                    wire:click="toggleSearch"
                    class="p-2 hover:bg-white/10 rounded-lg transition-colors group flex-shrink-0"
                    aria-label="Cerrar búsqueda">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </button>
                
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input 
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Buscar productos..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 focus:outline-none focus:border-[#BA2025] focus:bg-white/15 transition-all"
                        x-ref="searchInputMobile"
                        autofocus>
                </div>
            </div>
        </div>

        <!-- Contenido del search -->
        <div class="h-[calc(100vh-73px)] overflow-y-auto bg-white">
            <div class="p-4">
                @if($search && strlen($search) >= 2)
                    @if($isSearching)
                        <div class="py-20 text-center">
                            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-[#BA2025]"></div>
                            <p class="mt-4 text-gray-600 font-dm-sans">Buscando productos...</p>
                        </div>
                    @elseif($results->isEmpty())
                        <div class="py-20 text-center">
                            <svg class="mx-auto h-24 w-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="text-lg font-bold text-[#1b1b18] mb-2 font-dm-sans">No se encontraron productos</h3>
                            <p class="text-gray-600 text-sm">Intenta con otros términos</p>
                        </div>
                    @else
                        <div class="space-y-3 pb-6">
                            @foreach($results as $product)
<a 
href="{{ route('productos.show', $product) }}"
    wire:navigate
    wire:click="toggleSearch"
    class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg hover:border-[#BA2025] hover:shadow-md transition-all group bg-white">
    
    @if($product->images->first())
    <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg overflow-hidden">
        <img 
            src="{{ Storage::url($product->images->first()->image) }}" 
            alt="{{ $product->title }}"
            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
    </div>
    @else
    <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
    </div>
    @endif

    <div class="flex-1 min-w-0">
        <h4 class="font-bold text-[#1b1b18] group-hover:text-[#BA2025] transition-colors mb-1 line-clamp-2">
            {{ $product->title }}
        </h4>
        
        @if($product->ficha_tecnica)
        <p class="text-sm text-gray-600 line-clamp-2 mb-2">
            {{ Str::limit(strip_tags($product->ficha_tecnica), 80) }}
        </p>
        @endif

        @if($product->category)
        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
            {{ $product->category->title }}
        </span>
        @endif

        @if($product->destacado)
        <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded text-xs font-bold bg-[#BA2025] text-white">
            ★ Destacado
        </span>
        @endif
    </div>

    <svg class="w-5 h-5 text-gray-400 group-hover:text-[#BA2025] group-hover:translate-x-1 transition-all flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
</a>
@endforeach
                        </div>

                        <div class="sticky bottom-0 py-3 border-t border-gray-200 bg-gray-50/95 backdrop-blur-sm -mx-4 px-4">
                            <p class="text-sm text-gray-600 text-center font-dm-sans">
                                <span class="font-bold text-[#BA2025]">{{ $results->count() }}</span> resultado(s) encontrado(s)
                            </p>
                        </div>
                    @endif
                @else
                    <div class="py-20 text-center">
                        <svg class="mx-auto h-24 w-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="text-lg font-bold text-[#1b1b18] mb-2 font-dm-sans">Busca productos</h3>
                        <p class="text-gray-600 text-sm px-4">Escribe al menos 2 caracteres para comenzar la búsqueda</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    
    @script
    <script>
        $wire.on('search-opened', () => {
            setTimeout(() => {
                const inputDesktop = document.querySelector('[x-ref="searchInputDesktop"]');
                const inputMobile = document.querySelector('[x-ref="searchInputMobile"]');
                
                if (window.innerWidth >= 1024 && inputDesktop) {
                    inputDesktop.focus();
                } else if (inputMobile) {
                    inputMobile.focus();
                }
            }, 100);
        });
    
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                $wire.toggleSearch();
            }
        });
    </script>
    @endscript
    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.2s ease-out;
        }
    </style>
</div>