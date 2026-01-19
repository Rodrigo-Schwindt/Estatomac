<div x-data @click.outside="$wire.isOpen && $wire.toggleSearch()">
    <button wire:click="toggleSearch" 
            class="flex items-center justify-center rounded-lg transition-all duration-200 group cursor-pointer p-2 hover:bg-white/10">
        <svg class="w-6 h-6 text-white group-hover:text-[#BA2025] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </button>

    <div class="hidden lg:block absolute top-[90px] left-0 right-0 bg-gradient-to-b from-transparent to-white to-[30px] shadow-2xl transform transition-all duration-300 ease-in-out z-[60] border-b border-gray-200"
         style="max-height: {{ $isOpen ? '80vh' : '0' }}; overflow: hidden; opacity: {{ $isOpen ? '1' : '0' }}; visibility: {{ $isOpen ? 'visible' : 'hidden' }};">
        
        <div class="max-w-[1224px] mx-auto px-4 py-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="flex-1 relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Escribe el nombre o código del producto..."
                           class="w-full pl-6 pr-4 py-4 border-2 border-gray-100 rounded-xl text-lg focus:outline-none focus:border-[#BA2025] transition-all"
                           x-ref="searchInputDesktop">
                </div>
                <button wire:click="toggleSearch" class="p-4 text-gray-400 hover:text-red-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="overflow-y-auto max-h-[50vh] custom-scroll">
                @if($search && strlen($search) >= 2)
                    @if($isSearching)
                        <div class="py-12 text-center">
                            <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-red-100 border-t-[#BA2025]"></div>
                        </div>
                    @elseif($results->isEmpty())
                        <p class="text-center py-12 text-gray-500 text-lg">No encontramos productos para "{{ $search }}"</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($results as $product)
                                <a href="{{ route('productos.detalle', $product->id) }}" wire:navigate wire:click="toggleSearch"
                                   class="flex items-center gap-4 p-3 border border-gray-100 rounded-lg hover:border-[#BA2025] hover:shadow-md transition-all group bg-white">
                                    <div class="w-20 h-20 flex-shrink-0 bg-gray-50 rounded p-1">
                                        <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-900 truncate uppercase text-sm">{{ $product->title }}</h4>
                                        <p class="text-xs text-[#BA2025] font-semibold">{{ $product->code }}</p>
                                        @if($product->categoria)
                                            <span class="text-[10px] bg-gray-100 px-2 py-0.5 rounded text-gray-500 uppercase">{{ $product->categoria->title }}</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    @if($isOpen)
    <div class="lg:hidden fixed inset-0 z-[100] bg-white animate-fadeIn">
        <div class="p-4 border-b flex items-center gap-2">
            <button wire:click="toggleSearch" class="p-2 text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar..." 
                   class="flex-1 py-2 focus:outline-none text-lg" x-ref="searchInputMobile">
        </div>
        
        <div class="p-4 overflow-y-auto h-full">
            @foreach($results as $product)
                <a href="{{ route('productos.detalle', $product->id) }}" wire:navigate wire:click="toggleSearch" class="flex items-center gap-4 py-3 border-b border-gray-50">
                    <img src="{{ asset('storage/' . $product->image) }}" class="w-12 h-12 object-contain bg-gray-50 rounded">
                    <div>
                        <p class="font-bold text-sm text-gray-800 uppercase">{{ $product->title }}</p>
                        <p class="text-xs text-red-600">{{ $product->code }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @script
    <script>
        $wire.on('search-opened', () => {
            setTimeout(() => {
                const input = window.innerWidth >= 1024 
                    ? document.querySelector('[x-ref="searchInputDesktop"]') 
                    : document.querySelector('[x-ref="searchInputMobile"]');
                if (input) input.focus();
            }, 150);
        });
    </script>
    @endscript
</div>