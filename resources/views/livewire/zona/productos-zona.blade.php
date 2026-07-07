<div x-data="{ show: false }"
     x-init="setTimeout(() => show = true, 50)"
     x-show="show"
     x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="opacity-0 transform -translate-y-4"
     x-transition:enter-end="opacity-100 transform translate-y-0">

    {{-- BREADCRUMB --}}
    <div class="bg-white pt-[24px] pb-[24px]">
        <nav class="min-w-[1224px] mx-auto px-8 text-black font-inter text-[14px] flex items-center gap-1">
            <a wire:navigate href="{{ url('/') }}" class="text-black font-inter text-[14px] font-medium whitespace-nowrap">Inicio</a>
            <span class="font-light">|</span>
            <span class="font-light whitespace-nowrap">Productos</span>
        </nav>
    </div>

    {{-- BANNER: OPERANDO EN NOMBRE DE --}}
    @if($operandoEnNombreDe)
    <div class="bg-amber-50 border-b border-amber-200">
        <div class="min-w-[1224px] mx-auto px-8 py-[10px] flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-amber-800 text-[13px] font-medium">
                Ud. está operando en nombre de:
                <strong class="font-bold">{{ $vendedorOperativoNombre }}</strong>
                <span class="font-normal text-amber-600 ml-1">(sesión iniciada como {{ $vendedorLogueadoNombre }})</span>
            </span>
        </div>
    </div>
    @endif

    {{-- TABLA --}}
    <div class="min-w-[1224px] mx-auto px-8 pt-[32px] pb-[80px]">

        {{-- ENCABEZADO DEL CLIENTE --}}
        @if($clienteData)
        <div class="mb-[20px] bg-white border border-gray-200 rounded-[10px] px-5 py-4 text-[13px] font-inter">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-2">

                {{-- Fila 1 --}}
                <div class="col-span-2 md:col-span-2">
                    <span class="text-gray-500">Cliente:</span>
                    @if($clienteData['codigo'])
                        <span class="font-semibold text-gray-800 ml-1">[{{ $clienteData['codigo'] }}]</span>
                    @endif
                    <span class="font-semibold text-gray-800 ml-1">{{ $clienteData['nombre_fantasia'] ?: $clienteData['nombre'] }}</span>
                </div>
                <div class="col-span-2 md:col-span-2">
                    <span class="text-gray-500">Razón Social:</span>
                    <span class="font-medium text-gray-800 ml-1">{{ $clienteData['nombre'] }}</span>
                </div>

                {{-- Fila 2 --}}
                @if($clienteData['domicilio'])
                <div class="col-span-2 md:col-span-1">
                    <span class="text-gray-500">Domicilio:</span>
                    <span class="font-medium text-gray-800 ml-1">{{ $clienteData['domicilio'] }}</span>
                </div>
                @endif
                @if($clienteData['condicion_iva'])
                <div>
                    <span class="text-gray-500">Cond. IVA:</span>
                    <span class="font-medium text-gray-800 ml-1">{{ $clienteData['condicion_iva'] }}</span>
                </div>
                @endif
                @if($clienteData['localidad'])
                <div>
                    <span class="text-gray-500">Localidad:</span>
                    <span class="font-medium text-gray-800 ml-1">
                        {{ $clienteData['localidad'] }}{{ $clienteData['codigo_postal'] ? ' ('.$clienteData['codigo_postal'].')' : '' }}
                    </span>
                </div>
                @endif
                @if($clienteData['cuit'])
                <div>
                    <span class="text-gray-500">CUIT:</span>
                    <span class="font-medium text-gray-800 ml-1">{{ $clienteData['cuit'] }}</span>
                </div>
                @endif

                {{-- Fila 3 --}}
                @if($clienteData['provincia'])
                <div>
                    <span class="text-gray-500">Provincia:</span>
                    <span class="font-medium text-gray-800 ml-1">{{ $clienteData['provincia'] }}</span>
                </div>
                @endif
                @if($clienteData['condicion_venta'])
                <div>
                    <span class="text-gray-500">Cond. de Venta:</span>
                    <span class="font-medium text-gray-800 ml-1">{{ $clienteData['condicion_venta'] }}</span>
                </div>
                @endif
                @if($clienteData['transporte'])
                <div>
                    <span class="text-gray-500">Transporte:</span>
                    <span class="font-medium text-gray-800 ml-1">{{ $clienteData['transporte'] }}</span>
                </div>
                @endif
                @if($clienteData['canal'])
                <div>
                    <span class="text-gray-500">Canal:</span>
                    <span class="font-medium text-gray-800 ml-1">
                        {{ $clienteData['canal'] }}
                        @if($clienteData['descuento_canal'])
                            <span class="text-[#018637] font-semibold">({{ number_format($clienteData['descuento_canal'], 0) }}%)</span>
                        @endif
                    </span>
                </div>
                @endif

                {{-- Vendedor --}}
                @if($clienteData['vendedor_nombre'])
                <div class="col-span-2 md:col-span-4 pt-1 border-t border-gray-100 mt-1">
                    <span class="text-gray-500">Vendedor:</span>
                    <span class="font-medium text-gray-800 ml-1">{{ $clienteData['vendedor_nombre'] }}</span>
                    @if($operandoEnNombreDe)
                        <span class="ml-2 text-amber-600 text-[12px] font-medium">
                            — OPERANDO EN NOMBRE DE: {{ $vendedorOperativoNombre }}
                        </span>
                    @endif
                </div>
                @endif

            </div>
        </div>
        @endif

        {{-- BARRA DE FILTROS --}}
        <div class="mb-[18px]">

            {{-- Fila 1: Cliente (solo vendedor) --}}
            @if($esVendedor)
            <div class="flex justify-end mb-[10px]"
                 x-data="{
                    open: false,
                    search: '',
                    bloqueado: @js($carritoTieneItems),
                    clientes: @js($clientesLista),
                    selectedId: {{ $clienteSeleccionadoId ?? 'null' }},
                    get selectedLabel() {
                        if (!this.selectedId) return 'Seleccionar cliente';
                        const c = this.clientes.find(c => c.id === this.selectedId);
                        return c ? c.nombre + (c.codigo ? ' [' + c.codigo + ']' : '') : 'Seleccionar cliente';
                    },
                    get filtrados() {
                        const q = this.search.trim().toLowerCase();
                        const lista = q
                            ? this.clientes.filter(c =>
                                c.nombre.toLowerCase().includes(q) ||
                                c.codigo.includes(q))
                            : this.clientes;
                        return lista.slice(0, 60);
                    },
                    select(id) {
                        this.selectedId = id;
                        this.search = '';
                        this.open = false;
                        $wire.seleccionarCliente(id);
                    },
                    get selectedCliente() { return this.clientes.find(c => c.id === this.selectedId) || null; },
                    get selectedDescuento() { return this.selectedCliente ? this.selectedCliente.descuento_canal : 0; },
                    get selectedCanal() { return this.selectedCliente ? (this.selectedCliente.canal || '') : ''; }
                 }"
                 @click.outside="open = false">

                <div class="flex items-center gap-[10px]">
                    <span class="font-inter text-[15px] font-semibold text-[#23378C] whitespace-nowrap">Cliente:</span>

                    <div class="relative">
                        <button type="button"
                                @click="if(!bloqueado) { open = !open; if(open) $nextTick(() => $refs.clienteSearch?.focus()) }"
                                :title="bloqueado ? 'Vacíe el carrito para cambiar de cliente' : ''"
                                class="flex items-center gap-2 px-3 py-1.5 border rounded-[6px] font-inter text-[14px] focus:outline-none select-none"
                                :class="bloqueado ? 'border-gray-200 text-gray-400 bg-gray-50 cursor-not-allowed' : (selectedId ? 'border-[#018637] text-[#018637] bg-[#F0FAF4] cursor-pointer' : 'border-[#E0E0E0] text-[#555] bg-white cursor-pointer')">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span x-text="selectedLabel" class="truncate max-w-[220px]"></span>
                            <svg class="w-4 h-4 shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute right-0 top-[calc(100%+6px)] z-50 bg-white rounded-[10px] shadow-[0_8px_32px_rgba(0,0,0,0.14)] border border-[#F0F0F0] w-[340px]"
                             style="display:none;">
                            <div class="p-2 border-b border-[#F0F0F0]">
                                <div class="flex items-center gap-2 px-3 py-2 bg-[#F5F5F5] rounded-[6px]">
                                    <svg class="w-4 h-4 text-[#999] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="text" x-ref="clienteSearch" x-model="search" @click.stop
                                           placeholder="Buscar por nombre o código..."
                                           class="bg-transparent w-full text-[13px] font-inter text-[#333] focus:outline-none placeholder-[#bbb]">
                                </div>
                            </div>
                            <div class="max-h-[280px] overflow-y-auto py-1">
                                <template x-for="c in filtrados" :key="c.id">
                                    <button type="button" @click="select(c.id)"
                                            class="w-full text-left px-4 py-2.5 flex items-center justify-between gap-3 hover:bg-[#F5F5F5] transition-colors cursor-pointer"
                                            :class="c.id === selectedId ? 'bg-[#F0FAF4]' : ''">
                                        <div class="min-w-0">
                                            <p class="font-inter text-[13px] font-medium text-[#1a1a1a] truncate" x-text="c.nombre"></p>
                                            <p class="font-inter text-[11px] text-[#999]" x-text="'Cód. ' + (c.codigo || '—') + ' · Desc. ' + c.descuento_canal + '%'"></p>
                                        </div>
                                        <svg x-show="c.id === selectedId" class="w-4 h-4 text-[#018637] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </template>
                                <p x-show="filtrados.length === 0" class="text-center text-[#aaa] text-[13px] py-6">Sin resultados</p>
                            </div>
                        </div>
                    </div>

                    <span x-show="selectedId && selectedDescuento > 0" x-cloak
                          class="font-inter text-[13px] font-semibold text-[#018637] bg-[#F0FAF4] border border-[#018637]/20 px-2 py-0.5 rounded-full whitespace-nowrap">
                        <span x-text="selectedDescuento + '%'"></span>
                        <span x-show="selectedCanal" x-text="' · ' + selectedCanal"></span>
                        <span x-show="!selectedCanal"> canal</span>
                    </span>
                </div>
            </div>
            @endif

            {{-- Fila 2: Filtros + Buscador en la misma fila --}}
            <div class="flex items-center gap-[20px]">

                {{-- SELECTOR FAMILIA --}}
                <div class="flex items-center gap-[10px] shrink-0"
                     x-data="{
                        open: false,
                        q: '',
                        selected: '{{ $familiaId }}',
                        label: '{{ $familiaId ? $familias->firstWhere('id', $familiaId)?->titulo : 'Todas' }}',
                        all: [
                            { value: '', label: 'Todas' },
                            @foreach($familias as $f)
                            { value: '{{ $f->id }}', label: '{{ addslashes($f->titulo) }}' },
                            @endforeach
                        ],
                        get opts() {
                            const s = this.q.trim().toLowerCase();
                            return s ? this.all.filter(o => o.label.toLowerCase().includes(s)) : this.all;
                        },
                        pick(value, label) {
                            this.selected = value; this.label = label;
                            this.q = ''; this.open = false;
                            $wire.set('familiaId', value);
                        }
                     }"
                     @click.outside="open = false">

                    <span class="font-inter text-[18px] font-semibold text-black whitespace-nowrap">Familia:</span>
                    <div class="relative">
                        <button type="button"
                                @click="open = !open; if(open) $nextTick(() => $refs.qFamilia?.focus())"
                                class="flex items-center gap-1.5 font-inter text-[16px] text-black cursor-pointer focus:outline-none select-none whitespace-nowrap">
                            <span x-text="label"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                                 :class="open ? 'rotate-180' : ''" class="transition-transform duration-200 shrink-0">
                                <path d="M6 9L12 15L18 9" stroke="#DEDDDD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute left-0 top-[calc(100%+8px)] z-50 bg-white rounded-[8px] shadow-[0_8px_32px_rgba(0,0,0,0.12)] border border-[#F0F0F0] min-w-[200px]"
                             style="display:none;">
                            <div class="p-2 border-b border-[#F0F0F0]">
                                <input type="text" x-ref="qFamilia" x-model="q" @click.stop placeholder="Buscar..."
                                       class="w-full px-3 py-1.5 text-[13px] font-inter border border-[#E5E5E5] rounded-[5px] focus:outline-none focus:border-[#018637] placeholder-[#bbb]">
                            </div>
                            <div class="max-h-[220px] overflow-y-auto py-1">
                                <template x-for="opt in opts" :key="opt.value">
                                    <button type="button" @click="pick(opt.value, opt.label)"
                                            class="w-full text-left px-4 py-2 font-inter text-[14px] transition-colors cursor-pointer"
                                            :class="selected === opt.value ? 'text-[#018637] font-semibold bg-[#F0FAF4]' : 'text-[#1a1a1a] hover:bg-[#F5F5F5]'"
                                            x-text="opt.label">
                                    </button>
                                </template>
                                <p x-show="opts.length === 0" class="text-center text-[#aaa] text-[13px] py-4 px-3">Sin resultados</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SELECTOR CATEGORÍA --}}
                @if($categorias->isNotEmpty())
                <div class="flex items-center gap-[10px] shrink-0"
                     wire:key="cat-dropdown-{{ $familiaId }}-{{ $rubroId }}"
                     x-data="{
                        open: false,
                        q: '',
                        selected: '{{ $categoriaId }}',
                        label: '{{ $categoriaId ? $categorias->firstWhere('id', $categoriaId)?->titulo : 'Todas' }}',
                        all: [
                            { value: '', label: 'Todas' },
                            @foreach($categorias as $c)
                            { value: '{{ $c->id }}', label: '{{ addslashes($c->titulo) }}' },
                            @endforeach
                        ],
                        get opts() {
                            const s = this.q.trim().toLowerCase();
                            return s ? this.all.filter(o => o.label.toLowerCase().includes(s)) : this.all;
                        },
                        pick(value, label) {
                            this.selected = value; this.label = label;
                            this.q = ''; this.open = false;
                            $wire.set('categoriaId', value);
                        }
                     }"
                     @click.outside="open = false">

                    <span class="font-inter text-[18px] font-semibold text-black whitespace-nowrap">Categoría:</span>
                    <div class="relative">
                        <button type="button"
                                @click="open = !open; if(open) $nextTick(() => $refs.qCategoria?.focus())"
                                class="flex items-center gap-1.5 font-inter text-[16px] text-black cursor-pointer focus:outline-none select-none whitespace-nowrap">
                            <span x-text="label"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                                 :class="open ? 'rotate-180' : ''" class="transition-transform duration-200 shrink-0">
                                <path d="M6 9L12 15L18 9" stroke="#DEDDDD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute left-0 top-[calc(100%+8px)] z-50 bg-white rounded-[8px] shadow-[0_8px_32px_rgba(0,0,0,0.12)] border border-[#F0F0F0] min-w-[220px]"
                             style="display:none;">
                            <div class="p-2 border-b border-[#F0F0F0]">
                                <input type="text" x-ref="qCategoria" x-model="q" @click.stop placeholder="Buscar..."
                                       class="w-full px-3 py-1.5 text-[13px] font-inter border border-[#E5E5E5] rounded-[5px] focus:outline-none focus:border-[#018637] placeholder-[#bbb]">
                            </div>
                            <div class="max-h-[220px] overflow-y-auto py-1">
                                <template x-for="opt in opts" :key="opt.value">
                                    <button type="button" @click="pick(opt.value, opt.label)"
                                            class="w-full text-left px-4 py-2 font-inter text-[14px] transition-colors cursor-pointer"
                                            :class="selected === opt.value ? 'text-[#018637] font-semibold bg-[#F0FAF4]' : 'text-[#1a1a1a] hover:bg-[#F5F5F5]'"
                                            x-text="opt.label">
                                    </button>
                                </template>
                                <p x-show="opts.length === 0" class="text-center text-[#aaa] text-[13px] py-4 px-3">Sin resultados</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- SELECTOR RUBRO --}}
                @if($rubros->isNotEmpty())
                <div class="flex items-center gap-[10px] shrink-0"
                     wire:key="rubro-dropdown-{{ $familiaId }}-{{ $categoriaId }}"
                     x-data="{
                        open: false,
                        q: '',
                        selected: '{{ $rubroId }}',
                        label: '{{ $rubroId ? $rubros->firstWhere('id', $rubroId)?->titulo : 'Todos' }}',
                        all: [
                            { value: '', label: 'Todos' },
                            @foreach($rubros as $r)
                            { value: '{{ $r->id }}', label: '{{ addslashes($r->titulo) }}' },
                            @endforeach
                        ],
                        get opts() {
                            const s = this.q.trim().toLowerCase();
                            return s ? this.all.filter(o => o.label.toLowerCase().includes(s)) : this.all;
                        },
                        pick(value, label) {
                            this.selected = value; this.label = label;
                            this.q = ''; this.open = false;
                            $wire.set('rubroId', value);
                        }
                     }"
                     @click.outside="open = false">

                    <span class="font-inter text-[18px] font-semibold text-black whitespace-nowrap">Rubros:</span>
                    <div class="relative">
                        <button type="button"
                                @click="open = !open; if(open) $nextTick(() => $refs.qRubro?.focus())"
                                class="flex items-center gap-1.5 font-inter text-[16px] text-black cursor-pointer focus:outline-none select-none whitespace-nowrap">
                            <span x-text="label"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                                 :class="open ? 'rotate-180' : ''" class="transition-transform duration-200 shrink-0">
                                <path d="M6 9L12 15L18 9" stroke="#DEDDDD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute left-0 top-[calc(100%+8px)] z-50 bg-white rounded-[8px] shadow-[0_8px_32px_rgba(0,0,0,0.12)] border border-[#F0F0F0] min-w-[200px]"
                             style="display:none;">
                            <div class="p-2 border-b border-[#F0F0F0]">
                                <input type="text" x-ref="qRubro" x-model="q" @click.stop placeholder="Buscar..."
                                       class="w-full px-3 py-1.5 text-[13px] font-inter border border-[#E5E5E5] rounded-[5px] focus:outline-none focus:border-[#018637] placeholder-[#bbb]">
                            </div>
                            <div class="max-h-[220px] overflow-y-auto py-1">
                                <template x-for="opt in opts" :key="opt.value">
                                    <button type="button" @click="pick(opt.value, opt.label)"
                                            class="w-full text-left px-4 py-2 font-inter text-[14px] transition-colors cursor-pointer"
                                            :class="selected === opt.value ? 'text-[#018637] font-semibold bg-[#F0FAF4]' : 'text-[#1a1a1a] hover:bg-[#F5F5F5]'"
                                            x-text="opt.label">
                                    </button>
                                </template>
                                <p x-show="opts.length === 0" class="text-center text-[#aaa] text-[13px] py-4 px-3">Sin resultados</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- BUSCADOR (ocupa el ancho restante) --}}
                <div class="flex items-center overflow-hidden rounded-[14px] h-[38px] border border-[#E5E5E5] bg-white flex-1 min-w-0">
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Buscar por código o descripción..."
                           class="flex-1 min-w-0 px-4 py-2.5 font-inter text-[14px] text-[#1a1a1a] placeholder-[#bbb] focus:outline-none bg-transparent">
                    <button type="button"
                            wire:click="buscar"
                            class="flex items-center justify-center w-[44px] h-[44px] bg-[#018637] hover:bg-[#016d2e] transition-colors shrink-0 cursor-pointer">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                        </svg>
                    </button>
                </div>

            </div>{{-- fin fila filtros --}}

        </div>{{-- fin barra filtros --}}
        @if($productos && $productos->count())
            <div class="rounded-t-[4px] max-[1199px]:overflow-x-auto max-[1199px]:pb-3">
                <table class="table-fixed w-full border-collapse max-[1199px]:min-w-[1224px]">
                    <colgroup>
                        <col style="width:78px">
                        <col style="width:88px">
                        <col style="width:110px">
                        <col style="width:88px">
                        <col>{{-- nombre: ocupa el resto --}}
                        <col style="width:108px">
                        <col style="width:72px">
                        <col style="width:52px">
                        <col style="width:82px">
                        <col style="width:46px">
                        <col style="width:88px">
                        <col style="width:70px">
                        <col style="width:70px">
                        <col style="width:80px">
                        <col style="width:76px">
                    </colgroup>
                    <thead>
                        <tr class="bg-[#f8f8f8] h-[48px] text-[16px] text-[#131313] font-inter font-semibold leading-[100%]">
                            <th class="px-2 text-left"></th>
                            <th class="px-2 text-left pl-10">Código</th>
                            <th class="px-2 text-left pl-4">Cod. Color</th>
                            <th class="px-2 text-left">Categoría</th>
                            <th class="px-2 text-left w-[110px]">Nombre</th>
                            <th class="px-2 text-left">Presentación</th>
                            <th class="px-2 text-center">Precio</th>
                            <th class="px-2 text-center pl-2">Desc.</th>
                            <th class="px-2 text-right">Precio c/</br>desc.</th>
                            <th class="px-2 text-center">Bulto</th>
                            <th class="px-2 text-center">Cant. x Pres.</th>
                            <th class="px-2 text-center">Cantidad x Bulto</th>
                            <th class="px-2 text-center">Bulto<br>Pers.</th>
                            <th class="px-2 text-right">Subtotal</th>
                            <th class=" text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr><td colspan="15" class="h-[10px]"></td></tr>

                        @foreach($productos as $producto)
                            @php
                                $portada       = $producto->gallery->firstWhere('portada', true) ?? $producto->gallery->first();
                                $familia       = $producto->categorias->first()?->familia;
                                $precio        = floatval($producto->precio_unitario ?? 0);
                                $descBase      = $descuentoBase;
                                $descPersonalizado = $descuentosPersonalizados[$producto->id] ?? 0;
                                $precioDesc    = $precio * (1 - $descBase / 100) * (1 - $descPersonalizado / 100);
                                $bultoRaw      = trim((string) ($producto->bulto ?? ''));
                                $bulto         = $bultoRaw !== '' ? $bultoRaw : '1';
                                $bultoCantidad = max(1, intval($producto->bulto_cantidad ?? 1));
                                $cant          = $cantidades[$producto->id] ?? 0;
                                $cantBultos    = $cantidadesBultos[$producto->id] ?? 0;
                                $totalUnits    = $cant + ($cantBultos * $bultoCantidad);
                                $subtotal      = $totalUnits * $precioDesc;
                            @endphp

                            <tr class="align-middle border-b border-[#F0F0F0] hover:bg-[#FAFAFA] transition-colors">
                                {{-- Foto --}}
                                <td class="px-2 py-2">
                                    <div class="w-[80px] h-[80px] relative rounded-[4px]  flex items-center justify-center overflow-hidden" data-edge-bg-target>
                                        @if($portada)
                                            <img src="{{ Storage::url($portada->image) }}" alt="{{ $producto->codigo }}" data-edge-bg-image class="w-full h-full object-cover p-0.5">
                                        @else
                                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        @endif
                                                                                                <div class="absolute inset-0 bg-gray-400/10 pointer-events-none"></div>

                                    </div>
                                </td>

                                {{-- Código --}}
                                <td class="py-2 text-black font-inter text-[14px] pl-12 pr-4 font-semibold uppercase">
                                    {{ $producto->codigo ?? '-' }}
                                </td>

                                {{-- Cod. Color --}}
                                <td class="pl-4 pr-3 py-2 text-black font-inter text-center ">
                                    @if($producto->codigo_color)
                                        <span class="block text-[13px] font-medium capitalize leading-normal px-2">{{ $producto->nombre_color ?? $producto->codigo_color }}</span>
                                        <span class="block text-[12px] text-[#999] mt-1">({{ $producto->codigo_color }})</span>
                                    @else
                                        <span class="text-[#aaa]">—</span>
                                    @endif
                                </td>

                                {{-- Categoría (Familia) --}}
                                <td class="px-2 py-2 text-black font-inter text-[14px] font-medium">
                                    <span class="line-clamp-2">{{ $familia?->titulo ?? '-' }}</span>
                                </td>

                                {{-- Nombre --}}
                                <td class="px-2 py-2 text-black font-inter text-[14px] w-[110px]">
                                    <span class="line-clamp-3">{{ strip_tags($producto->descripcion) }}</span>
                                </td>

                                {{-- Presentación --}}
                                <td class="px-2 py-2 text-[#111] font-inter text-[14px]">
                                    <span class="line-clamp-3">{{ strip_tags($producto->presentacion) }}</span>
                                </td>

                                {{-- Precio --}}
                                <td class="px-2 py-2 text-black font-inter text-[15px] text-center font-bold">
                                    ${{ number_format($precio, 2, ',', '.') }}
                                </td>

                                {{-- Descuento --}}
                                <td class="px-2 py-2 text-center pl-2">
                                    @if($descBase > 0)
                                        <span class="inline-block text-[#308C05] font-inter text-[14px] font-semibold px-1.5 py-0.5 rounded-full">
                                            {{ number_format($descBase, 0) }}%
                                        </span>
                                    @else
                                        <span class="text-[#aaa] text-[11px]">—</span>
                                    @endif

                                    @if($esVendedor)
                                        <div wire:key="desc-pers-{{ $producto->id }}-{{ $clienteSeleccionadoId }}"
                                             x-data="{
                                                val: {{ (float) $descPersonalizado }},
                                                save() {
                                                    $wire.setDescuentoPersonalizado({{ $producto->id }}, this.val || 0);
                                                }
                                             }"
                                             class="mt-1 flex flex-col items-center gap-0.5">
                                            <input type="number"
                                                   x-model.number="val"
                                                   @blur="save()"
                                                   @keydown.enter.prevent="save()"
                                                   min="0" max="100" step="0.5"
                                                   placeholder="+%"
                                                   title="Descuento personalizado"
                                                   class="w-[40px] h-[22px] text-center border border-[#D7E8DC] rounded text-[11px] font-semibold text-[#308C05] py-0.5 focus:outline-none focus:ring-1 focus:ring-[#018637] [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                            <span class="font-inter text-[9px] leading-none text-[#777]">pers.</span>
                                        </div>
                                    @endif
                                </td>

                                {{-- Precio c/desc --}}
                                <td class="px-2 py-2 text-[#111]  text-[15px] font-bold text-right">
                                    ${{ number_format($precioDesc, 2, ',', '.') }}
                                </td>

                                {{-- Bulto --}}
                                <td class="px-2 py-2 text-black font-inter text-[14px] text-center">
                                    {{ $bulto }}
                                </td>

                                {{-- Cant. x Presentación (contador) --}}
                                <td class="px-1 py-2 text-center">
                                    <div class="inline-flex items-center  border border-[#E8E8E8] rounded-[4px] h-[44px] w-[64px]  bg-white">
                                        <button type="button"
                                                wire:click="decrementar({{ $producto->id }})"
                                                class="text-[#111] font-inter text-[18px] font-normal leading-none w-5 flex items-center justify-center hover:text-black select-none">
                                            −
                                        </button>
                                        <span class="font-inter text-[16px] font-medium text-[#1a1a1a] w-6 text-center tabular-nums">
                                            {{ $cant }}
                                        </span>
                                        <button type="button"
                                                wire:click="incrementar({{ $producto->id }})"
                                                class="text-[#111] font-inter text-[18px] font-normal leading-none w-5 flex items-center justify-center hover:text-black select-none">
                                            +
                                        </button>
                                    </div>
                                </td>

                                {{-- Cant. x Bultos --}}
                                <td class="px-1 py-2 text-center">
                                    <div class="inline-flex items-center  border border-[#E8E8E8] rounded-[4px] h-[44px] w-[64px]  bg-white">
                                        <button type="button"
                                                wire:click="decrementarBulto({{ $producto->id }})"
                                                class="text-[#111] font-inter text-[18px] font-normal leading-none w-5 flex items-center justify-center hover:text-black select-none">
                                            −
                                        </button>
                                        <span class="font-inter text-[16px] font-medium text-[#1a1a1a] w-6 text-center tabular-nums">
                                            {{ $cantBultos }}
                                        </span>
                                        <button type="button"
                                                wire:click="incrementarBulto({{ $producto->id }})"
                                                class="text-[#111] font-inter text-[18px] font-normal leading-none w-5 flex items-center justify-center hover:text-black select-none">
                                            +
                                        </button>
                                    </div>
                                </td>

                                {{-- Bulto Personalizado --}}
                                <td class="px-2 py-2 text-center">
                                    @if($producto->codigo_color)
                                        <button type="button"
                                                wire:click="abrirBultoModal({{ $producto->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="abrirBultoModal({{ $producto->id }})"
                                                class="rounded-[4px] cursor-pointer border border-[#018637] w-[40px] h-[40px] text-[#018637] flex items-center justify-center hover:bg-[#018637] hover:text-white transition-colors mx-auto disabled:opacity-50 disabled:cursor-wait"
                                                title="Armar bulto personalizado por color">
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="2.5" cy="2.5" r="1.5" fill="currentColor"/>
                                                <circle cx="7" cy="2.5" r="1.5" fill="currentColor"/>
                                                <circle cx="11.5" cy="2.5" r="1.5" fill="currentColor"/>
                                                <circle cx="2.5" cy="7" r="1.5" fill="currentColor"/>
                                                <circle cx="7" cy="7" r="1.5" fill="currentColor"/>
                                                <circle cx="11.5" cy="7" r="1.5" fill="currentColor"/>
                                                <circle cx="2.5" cy="11.5" r="1.5" fill="currentColor"/>
                                                <circle cx="7" cy="11.5" r="1.5" fill="currentColor"/>
                                                <circle cx="11.5" cy="11.5" r="1.5" fill="currentColor"/>
                                            </svg>
                                        </button>
                                    @endif
                                </td>

                                {{-- Subtotal --}}
                                <td class="px-2 py-2 text-black font-inter text-[15px] font-bold text-right">
                                    ${{ number_format($subtotal, 2, ',', '.') }}
                                </td>

                                {{-- Botón carrito --}}
                                <td class=" py-2 text-end">
                                    <button
                                        wire:click="agregarAlCarrito({{ $producto->id }})"
                                        class="rounded-[4px] cursor-pointer border-1 border-[#23378C] w-[40px] h-[40px] text-[#23378C] flex items-center justify-center  hover:bg-[#23378C] hover:text-white transition-colors mx-auto">
                                        <span class="text-[20px] font-semibold leading-none">+</span>
                                        <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4.50416 16C4.09128 16 3.73795 15.8435 3.44418 15.5304C3.15041 15.2173 3.00327 14.8405 3.00277 14.4C3.00277 13.96 3.14991 13.5835 3.44418 13.2704C3.73845 12.9573 4.09178 12.8005 4.50416 12.8C4.91704 12.8 5.27062 12.9568 5.56489 13.2704C5.85916 13.584 6.00605 13.9605 6.00555 14.4C6.00555 14.84 5.85866 15.2168 5.56489 15.5304C5.27112 15.844 4.91754 16.0005 4.50416 16ZM12.0111 16C11.5982 16 11.2449 15.8435 10.9511 15.5304C10.6573 15.2173 10.5102 14.8405 10.5097 14.4C10.5097 13.96 10.6568 13.5835 10.9511 13.2704C11.2454 12.9573 11.5987 12.8005 12.0111 12.8C12.424 12.8 12.7776 12.9568 13.0718 13.2704C13.3661 13.584 13.513 13.9605 13.5125 14.4C13.5125 14.84 13.3656 15.2168 13.0718 15.5304C12.7781 15.844 12.4245 16.0005 12.0111 16ZM3.86607 3.2L5.66774 7.2H10.9226L12.987 3.2H3.86607ZM3.15291 1.6H14.2256C14.5134 1.6 14.7324 1.7368 14.8825 2.0104C15.0326 2.284 15.0389 2.56053 14.9013 2.84L12.2363 7.96C12.0987 8.22667 11.9143 8.43333 11.683 8.58C11.4518 8.72667 11.1983 8.8 10.9226 8.8H5.32992L4.50416 10.4H13.5125V12H4.50416C3.94114 12 3.51575 11.7368 3.22798 11.2104C2.94022 10.684 2.9277 10.1605 3.19045 9.64L4.20388 7.68L1.50139 1.6H0V0H2.43975L3.15291 1.6Z" fill="currentColor"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}
            <div class="mt-[40px]">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-[20px]">
                    <div class="text-[#666] font-inter text-[13px]">
                        Mostrando
                        <span class="font-semibold text-black">{{ $productos->firstItem() }}</span>
                        a
                        <span class="font-semibold text-black">{{ $productos->lastItem() }}</span>
                        de
                        <span class="font-semibold text-black">{{ $productos->total() }}</span>
                        productos
                    </div>
                    <div class="flex items-center gap-[10px]">
                        <span class="text-[#666] font-inter text-[13px]">Mostrar:</span>
                        <select wire:model.live="perPage" class="h-[36px] px-[10px] rounded-[4px] border border-[#E5E5E5] bg-white font-inter text-[13px] cursor-pointer">
                            <option value="12">12</option>
                            <option value="24">24</option>
                            <option value="48">48</option>
                            <option value="96">96</option>
                        </select>
                    </div>
                </div>

                @if($productos->hasPages())
                    <div class="flex items-center justify-center gap-[6px] flex-wrap">
                        @if($productos->onFirstPage())
                            <button disabled class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 12L4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                            <button disabled class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        @else
                            <button wire:click="gotoPage(1)" class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#018637] hover:text-white hover:border-[#018637] transition-colors">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 12L4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                            <button wire:click="previousPage" class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#018637] hover:text-white hover:border-[#018637] transition-colors">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        @endif

                        @php
                            $currentPage = $productos->currentPage();
                            $lastPage    = $productos->lastPage();
                            $startPage   = max(1, $currentPage - 2);
                            $endPage     = min($lastPage, $currentPage + 2);
                        @endphp

                        @if($startPage > 1)
                            <button wire:click="gotoPage(1)" class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#018637] hover:text-white hover:border-[#018637] transition-colors font-inter text-[13px]">1</button>
                            @if($startPage > 2)<span class="text-[#666] text-[13px]">...</span>@endif
                        @endif

                        @for($page = $startPage; $page <= $endPage; $page++)
                            @if($page == $currentPage)
                                <button class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] bg-[#018637] text-white font-inter text-[13px] font-semibold">{{ $page }}</button>
                            @else
                                <button wire:click="gotoPage({{ $page }})" class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#018637] hover:text-white hover:border-[#018637] transition-colors font-inter text-[13px]">{{ $page }}</button>
                            @endif
                        @endfor

                        @if($endPage < $lastPage)
                            @if($endPage < $lastPage - 1)<span class="text-[#666] text-[13px]">...</span>@endif
                            <button wire:click="gotoPage({{ $lastPage }})" class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#018637] hover:text-white hover:border-[#018637] transition-colors font-inter text-[13px]">{{ $lastPage }}</button>
                        @endif

                        @if($productos->hasMorePages())
                            <button wire:click="nextPage" class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#018637] hover:text-white hover:border-[#018637] transition-colors">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <button wire:click="gotoPage({{ $productos->lastPage() }})" class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] border border-[#E5E5E5] hover:bg-[#018637] hover:text-white hover:border-[#018637] transition-colors">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        @else
                            <button disabled class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <button disabled class="w-[36px] h-[36px] flex items-center justify-center rounded-[4px] bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M6 4L10 8L6 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        @else
            <div class="text-center text-[#777] py-20 text-[15px]">
                No se encontraron productos
            </div>
        @endif
    </div>

    {{-- MODAL BULTO PERSONALIZADO --}}
    <div
        wire:ignore
        x-data="{
            open: false,
            productoId: null,
            bultoNombre: '',
            bultoCantidad: 1,
            variantes: [],
            get totalUnidades() { return this.variantes.reduce((s, v) => s + v.cantidad, 0); },
            get bultosCompletos() { return Math.floor(this.totalUnidades / this.bultoCantidad); },
            get restante() { return this.totalUnidades % this.bultoCantidad; },
            get esValido() { return this.totalUnidades > 0 && this.restante === 0; },
            get progresoBar() { return this.esValido ? 100 : (this.restante / this.bultoCantidad * 100); },
            incrementar(idx) { this.variantes[idx].cantidad++; },
            decrementar(idx) { if (this.variantes[idx].cantidad > 0) this.variantes[idx].cantidad--; },
            init() {
                Livewire.on('abrir-bulto-modal', (data) => {
                    const payload = Array.isArray(data) ? data[0] : data;
                    this.productoId    = payload.productoId;
                    this.bultoNombre   = payload.bultoNombre;
                    this.bultoCantidad = payload.bultoCantidad;
                    this.variantes     = payload.variantes.map(v => ({...v, cantidad: 0}));
                    this.open          = true;
                });
            },
            async confirmar() {
                if (!this.esValido) return;
                const distribuciones = this.variantes
                    .filter(v => v.cantidad > 0)
                    .map(v => ({id: v.id, cantidad: v.cantidad}));
                await $wire.agregarBultoPersonalizado(this.productoId, distribuciones);
                this.open = false;
            }
        }"
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-[200] flex items-center justify-center p-4"
        style="display:none;"
    >
        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/40" @click="open = false"></div>

        {{-- Modal Card --}}
        <div
            class="relative bg-white rounded-[12px] shadow-[0_24px_64px_rgba(0,0,0,0.18)] w-full max-w-[560px] max-h-[88vh] flex flex-col"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.stop
        >
            {{-- Header --}}
            <div class="flex items-start justify-between px-6 pt-5 pb-4 border-b border-[#F0F0F0]">
                <div>
                    <h3 class="font-inter font-semibold text-[17px] text-[#131313]">Bulto Personalizado</h3>
                    <p class="font-inter text-[13px] text-[#666] mt-0.5">
                        <span x-text="bultoNombre" class="font-medium text-[#131313]"></span>
                        &mdash; cada bulto = <span x-text="bultoCantidad" class="font-semibold text-[#131313]"></span> unidades
                    </p>
                </div>
                <button @click="open = false" class="text-[#bbb] hover:text-[#444] transition-colors mt-0.5 cursor-pointer">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>

            {{-- Lista de colores (scrollable) --}}
            <div class="overflow-y-auto flex-1 px-6 py-3">
                <template x-for="(variante, idx) in variantes" :key="variante.id">
                    <div class="flex items-center gap-3 py-2.5 border-b border-[#F5F5F5] last:border-0">
                        {{-- Imagen --}}
                        <div class="w-[48px] h-[48px] rounded-[4px] overflow-hidden flex-shrink-0 bg-[#F5F5F5] flex items-center justify-center">
                            <template x-if="variante.imagen">
                                <img :src="variante.imagen" :alt="variante.nombre_color" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!variante.imagen">
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </template>
                        </div>

                        {{-- Info color --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-inter text-[14px] font-semibold text-[#131313] capitalize truncate" x-text="variante.nombre_color"></p>
                            <p class="font-inter text-[12px] text-[#999]" x-text="variante.codigo_color"></p>
                        </div>

                        {{-- Control cantidad --}}
                        <div class="flex items-center border border-[#E8E8E8] rounded-[4px] h-[40px] w-[96px] bg-white flex-shrink-0">
                            <button type="button"
                                    @click="decrementar(idx)"
                                    class="text-[#111] text-[18px] leading-none w-8 flex items-center justify-center hover:text-black select-none h-full cursor-pointer">−</button>
                            <span class="font-inter text-[15px] font-medium text-[#1a1a1a] flex-1 text-center tabular-nums" x-text="variante.cantidad"></span>
                            <button type="button"
                                    @click="incrementar(idx)"
                                    class="text-[#111] text-[18px] leading-none w-8 flex items-center justify-center hover:text-black select-none h-full cursor-pointer">+</button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-[#F0F0F0] bg-[#FAFAFA] rounded-b-[12px]">
                {{-- Indicador de progreso --}}
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="font-inter text-[13px] text-[#666]">Total seleccionado</span>
                        <span class="font-inter text-[13px] font-semibold"
                              :class="esValido ? 'text-[#018637]' : 'text-[#131313]'">
                            <span x-text="totalUnidades"></span> ud<span x-show="totalUnidades !== 1">s</span>.
                            <span x-show="bultosCompletos > 0" class="text-[#018637]">
                                = <span x-text="bultosCompletos"></span> bulto<span x-show="bultosCompletos > 1">s</span>
                            </span>
                        </span>
                    </div>
                    <div class="h-[6px] rounded-full bg-[#EBEBEB] overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-200"
                             :class="esValido ? 'bg-[#018637]' : 'bg-[#f59e0b]'"
                             :style="`width: ${progresoBar}%`"></div>
                    </div>
                    <p class="font-inter text-[12px] mt-1.5"
                       :class="esValido ? 'text-[#018637]' : 'text-[#d97706]'"
                       x-show="totalUnidades > 0">
                        <template x-if="esValido">
                            <span>&#10003; Listo para agregar al carrito</span>
                        </template>
                        <template x-if="!esValido">
                            <span>Faltan <span x-text="bultoCantidad - restante"></span> unidad<span x-show="(bultoCantidad - restante) !== 1">es</span> para completar el bulto</span>
                        </template>
                    </p>
                </div>

                {{-- Botones --}}
                <div class="flex items-center gap-3 justify-end">
                    <button type="button"
                            @click="open = false"
                            class="px-5 py-2 font-inter text-[14px] font-medium text-[#555] border border-[#E5E5E5] rounded-[6px] hover:bg-[#F0F0F0] transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="button"
                            @click="confirmar()"
                            :disabled="!esValido"
                            :class="esValido
                                ? 'bg-[#018637] hover:bg-[#016d2e] text-white cursor-pointer'
                                : 'bg-[#E5E5E5] text-[#aaa] cursor-not-allowed'"
                            class="px-5 py-2 font-inter text-[14px] font-semibold rounded-[6px] transition-colors flex items-center gap-2">
                        <svg width="15" height="15" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.50416 16C4.09128 16 3.73795 15.8435 3.44418 15.5304C3.15041 15.2173 3.00327 14.8405 3.00277 14.4C3.00277 13.96 3.14991 13.5835 3.44418 13.2704C3.73845 12.9573 4.09178 12.8005 4.50416 12.8C4.91704 12.8 5.27062 12.9568 5.56489 13.2704C5.85916 13.584 6.00605 13.9605 6.00555 14.4C6.00555 14.84 5.85866 15.2168 5.56489 15.5304C5.27112 15.844 4.91754 16.0005 4.50416 16ZM12.0111 16C11.5982 16 11.2449 15.8435 10.9511 15.5304C10.6573 15.2173 10.5102 14.8405 10.5097 14.4C10.5097 13.96 10.6568 13.5835 10.9511 13.2704C11.2454 12.9573 11.5987 12.8005 12.0111 12.8C12.424 12.8 12.7776 12.9568 13.0718 13.2704C13.3661 13.584 13.513 13.9605 13.5125 14.4C13.5125 14.84 13.3656 15.2168 13.0718 15.5304C12.7781 15.844 12.4245 16.0005 12.0111 16ZM3.86607 3.2L5.66774 7.2H10.9226L12.987 3.2H3.86607ZM3.15291 1.6H14.2256C14.5134 1.6 14.7324 1.7368 14.8825 2.0104C15.0326 2.284 15.0389 2.56053 14.9013 2.84L12.2363 7.96C12.0987 8.22667 11.9143 8.43333 11.683 8.58C11.4518 8.72667 11.1983 8.8 10.9226 8.8H5.32992L4.50416 10.4H13.5125V12H4.50416C3.94114 12 3.51575 11.7368 3.22798 11.2104C2.94022 10.684 2.9277 10.1605 3.19045 9.64L4.20388 7.68L1.50139 1.6H0V0H2.43975L3.15291 1.6Z" fill="currentColor"/>
                        </svg>
                        Agregar al carrito
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- TOAST --}}
    <div
        x-data="{
            show: false,
            message: '',
            type: 'success',
            progress: 100,
            progressInterval: null,
            init() {
                Livewire.on('producto-agregado', (data) => {
                    const payload = Array.isArray(data) ? data[0] : data;
                    this.lanzarToast(payload.message || 'Operación exitosa', payload.type || 'success');
                });
            },
            lanzarToast(msg, tipo) {
                this.message = msg;
                this.type = tipo;
                this.show = true;
                this.progress = 100;
                if (this.progressInterval) clearInterval(this.progressInterval);
                this.progressInterval = setInterval(() => {
                    this.progress -= 3.33;
                    if (this.progress <= 0) clearInterval(this.progressInterval);
                }, 100);
                setTimeout(() => { this.show = false; }, 3000);
            }
        }"
        x-cloak
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90 -translate-y-8"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed left-1/2 transform -translate-x-1/2 z-[100] px-4"
        style="top: 100px; display: none;"
    >
        <div class="bg-white rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden min-w-[300px]">
            <div class="h-1 bg-gray-200">
                <div class="h-full transition-all duration-100 ease-linear rounded-r-full"
                     :class="type === 'success' ? 'bg-[#018637]' : 'bg-red-500'"
                     :style="`width: ${progress}%`"></div>
            </div>
            <div class="px-5 py-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     :class="type === 'success' ? 'bg-[#018637]' : 'bg-red-500'">
                    <svg x-show="type === 'success'" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <svg x-show="type === 'error'" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6L18 18" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <p class="text-gray-900 font-inter text-[14px] font-semibold" x-text="message"></p>
            </div>
        </div>
    </div>

    @include('partials.todotex-edge-backgrounds')
</div>
