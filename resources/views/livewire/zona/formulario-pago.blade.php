<div class="bg-white pt-[24px] pb-[93px] max-[1199px]:pb-16 max-[639px]:pb-12"  x-data="{ show: false }"
x-init="setTimeout(() => show = true, 50)"
x-show="show"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0">
    <div class="bg-white pb-[61px] max-[1199px]:pb-10 max-[1199px]:px-4 max-[639px]:pb-8">
        <nav class="max-w-[1224px] mx-auto text-black font-montserrat text-[12px] max-[639px]:text-[11px] leading-[150%] flex items-center gap-1">
            <a wire:navigate href="{{ url('/') }}" class="text-black font-inter text-[12px] max-[639px]:text-[11px] font-medium leading-normal drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Inicio</a>
            <span class="text-black font-inter text-[12px] max-[639px]:text-[11px] font-light leading-normal drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">|</span>
            <span class="text-black font-inter text-[12px] max-[639px]:text-[11px] font-light leading-normal drop-shadow-[0_6px_20px_rgba(0,0,0,0.75)]">Información de pagos</span>
        </nav>
    </div>
    <div class="max-w-[1224px] mx-auto flex flex-col md:flex-row gap-[24px] max-[1199px]:px-4 max-[1199px]:gap-6">
        
        <div class="md:w-1/3">
            <div class="space-y-[53px] w-[404px] max-[1199px]:w-full max-[1199px]:space-y-8 max-[639px]:space-y-6 animate-fadeIn">
                @if($config && ($config->title || $config->description))
                <section>
                    <h2 class="text-[#111010] font-inter text-[18px] max-[639px]:text-[16px] font-semibold leading-[120%] pb-[24px] max-[639px]:pb-2">
                        {{ $config->title ?? 'Información de pagos' }}
                    </h2>
                    <div class="text-black font-inter text-[16px] max-[639px]:text-[14px] font-normal leading-[150%]">
                        {!! $config->description ?? '' !!}
                    </div>
                </section>
                @endif

                @if($config && ($config->title2 || $config->description2))
                <div class="h-[1px] bg-gray-200 w-full"></div>

                <section>
                    <h2 class="text-[#111010] font-inter text-[18px] max-[639px]:text-[16px] font-semibold leading-[120%] pb-[24px] max-[639px]:pb-2">
                        {{ $config->title2 ?? 'Condiciones de pago vigentes' }}
                    </h2>
                    <div class="text-black font-inter text-[15px] max-[639px]:text-[14px] font-normal leading-[150%]">
                        {!! $config->description2 ?? '' !!}
                    </div>
                </section>
                @endif

                @if(!$config || (!$config->title && !$config->title2))
                <section>
                    <h2 class="text-[#111010] font-inter text-[18px] font-semibold leading-[120%] pb-[7px]">Información de pagos</h2>
                    <p class="text-black font-inter text-[16px] font-normal leading-[150%]">Completá el formulario para informar tu pago.</p>
                </section>
                @endif
            </div>
        </div>

        <div class="md:w-2/3 bg-[#F5F5F5] px-[18px] max-[639px]:px-4 pt-[28px] max-[639px]:pt-5 pb-[42px] max-[639px]:pb-6 rounded-[4px] animate-fadeIn" style="animation-delay: 0.2s;">
            <form action="{{ route('cliente.pago.enviar') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 max-[1199px]:gap-y-4">
                @csrf
                
                <div class="flex flex-col">
                    <label class="text-[#111010] font-inter text-[16px] max-[639px]:text-[14px] font-normal leading-[150%] mb-[8px] max-[639px]:mb-2">Fecha*</label>
                    <input type="date" name="fecha" value="{{ old('fecha') }}" class="h-[45px] max-[639px]:h-[42px] rounded-[4px] border border-[#E9E9E9] px-3 transition-all duration-200 focus:border-[#23378C] focus:outline-none focus:ring-2 focus:ring-[#23378C]/20" required>
                    @error('fecha') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            
                <div class="flex flex-col">
                    <label class="text-[#111010] font-inter text-[16px] max-[639px]:text-[14px] font-normal leading-[150%] mb-[8px] max-[639px]:mb-2">Importe*</label>
                    <input type="number" step="0.01" name="importe" value="{{ old('importe') }}" class="h-[45px] max-[639px]:h-[42px] rounded-[4px] border border-[#E9E9E9] px-3 transition-all duration-200 focus:border-[#23378C] focus:outline-none focus:ring-2 focus:ring-[#23378C]/20" required>
                    @error('importe') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-[24px] max-[1199px]:gap-4">
                    <div class="flex flex-col">
                        <label class="text-[#111010] font-inter text-[16px] max-[639px]:text-[14px] font-normal leading-[150%] mb-[8px] max-[639px]:mb-2">Banco*</label>
                        <input type="text" name="banco" value="{{ old('banco') }}" class="h-[45px] max-[639px]:h-[42px] rounded-[4px] border border-[#E9E9E9] px-3 transition-all duration-200 focus:border-[#23378C] focus:outline-none focus:ring-2 focus:ring-[#23378C]/20" required>
                        @error('banco') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="flex flex-col">
                        <label class="text-[#111010] font-inter text-[16px] max-[639px]:text-[14px] font-normal leading-[150%] mb-[8px] max-[639px]:mb-2">Sucursal*</label>
                        <input type="text" name="sucursal" value="{{ old('sucursal') }}" class="h-[45px] max-[639px]:h-[42px] rounded-[4px] border border-[#E9E9E9] px-3 transition-all duration-200 focus:border-[#23378C] focus:outline-none focus:ring-2 focus:ring-[#23378C]/20" required>
                        @error('sucursal') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="flex flex-col">
                        <label class="text-[#111010] font-inter text-[16px] max-[639px]:text-[14px] font-normal leading-[150%] mb-[8px] max-[639px]:mb-2">Facturas canceladas</label>
                        <input type="text" name="facturas" value="{{ old('facturas') }}" class="h-[45px] max-[639px]:h-[42px] rounded-[4px] border border-[#E9E9E9] px-3 transition-all duration-200 focus:border-[#23378C] focus:outline-none focus:ring-2 focus:ring-[#23378C]/20">
                    </div>
                </div>
            
                <div class="flex flex-col md:col-span-2">
                    <label class="text-[#111010] font-inter text-[16px] max-[639px]:text-[14px] font-normal leading-[150%] mb-[8px] max-[639px]:mb-2">Observaciones / Aclaraciones</label>
                    <textarea name="observaciones" rows="5" class="p-4 max-[639px]:p-3 rounded-[4px] border border-[#E9E9E9] h-[170px] max-[639px]:h-[140px] w-full transition-all duration-200 focus:border-[#23378C] focus:outline-none focus:ring-2 focus:ring-[#23378C]/20">{{ old('observaciones') }}</textarea>
                </div>
                
                <div class="md:col-span-2 flex flex-col md:flex-row items-end justify-between gap-6 max-[1199px]:gap-4 mt-4 max-[1199px]:mt-0">
                    
                    <div class="flex flex-col w-full md:max-w-[350px] max-[1199px]:max-w-none">
                        <label class="text-[#111010] font-inter text-[16px] max-[639px]:text-[14px] font-normal leading-[150%] mb-[8px] max-[639px]:mb-2">Adjuntar archivo*</label>
                        <div class="relative flex items-center h-[45px] max-[639px]:h-[42px] border border-[#E9E9E9] rounded-[4px] px-4 max-[639px]:px-3 justify-between bg-white transition-all duration-200 hover:border-[#23378C]">
                            <span class="text-[#5C5C5C] text-sm max-[639px]:text-xs truncate" id="file-name">Seleccionar archivo</span>
                            <input type="file" name="archivo" id="archivo_pago" class="hidden" accept=".pdf,.jpg,.jpeg,.png" required onchange="document.getElementById('file-name').textContent = this.files[0]?.name || 'Seleccionar archivo'">
                            <label for="archivo_pago" class="cursor-pointer flex-shrink-0">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#018637" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="max-[639px]:w-[18px] max-[639px]:h-[18px]">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                            </label>
                        </div>
                        @error('archivo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
            
                    <div class="flex flex-col md:flex-row items-center justify-start gap-[49px] max-[1199px]:gap-3 max-[1199px]:w-full max-[639px]:flex-col max-[639px]:items-stretch">
                        <span class="text-[#111010] font-inter text-[15px] max-[639px]:text-[13px] font-normal leading-normal max-[639px]:text-center">* campos obligatorios</span>
                        <button type="submit" class="w-[167px] max-[1199px]:w-full h-[40px] max-[639px]:h-[40px] rounded-[40px] bg-[#23378C] text-white text-center font-inter text-[16px] max-[639px]:text-[13px] font-semibold leading-normal  transition-all duration-200  hover:shadow-lg">
                            Enviar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(session('success') || session('error'))
    <div 
        x-data="{ 
            show: false, 
            message: '{{ session('success') ?? session('error') }}',
            type: '{{ session('success') ? 'success' : 'error' }}',
            progress: 100,
            progressInterval: null,
            init() {
                setTimeout(() => {
                    this.show = true;
                    this.animarProgreso();
                }, 100);
            },
            animarProgreso() {
                this.progress = 100;
                if (this.progressInterval) clearInterval(this.progressInterval);
                this.progressInterval = setInterval(() => {
                    this.progress -= 3.33;
                    if (this.progress <= 0) {
                        clearInterval(this.progressInterval);
                    }
                }, 100);
                
                setTimeout(() => { 
                    this.show = false;
                    clearInterval(this.progressInterval);
                }, 3000);
            }
        }"
        x-show="show"
        x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="opacity-0 scale-90 -translate-y-8"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="fixed left-1/2 transform -translate-x-1/2 z-[100] max-[639px]:left-4 max-[639px]:right-4 max-[639px]:transform-none max-[639px]:translate-x-0"
        style="top: 104px;"
    >
        <div class="relative bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.15)] border border-white/50 backdrop-blur-xl overflow-hidden min-w-[380px] max-w-[480px] max-[639px]:min-w-0 max-[639px]:max-w-none">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-gray-200 to-gray-300">
                <div 
                    class="h-full transition-all duration-100 ease-linear rounded-r-full"
                    :class="{
                        'bg-gradient-to-r from-[#00C853] via-[#00E676] to-[#69F0AE]': type === 'success',
                        'bg-gradient-to-r from-[#23378C] via-[#FF1744] to-[#F50057]': type === 'error'
                    }"
                    :style="`width: ${progress}%`"
                ></div>
            </div>
            
            <div class="px-5 py-4 max-[639px]:px-4 max-[639px]:py-3 flex items-center gap-4 max-[639px]:gap-3">
                <div class="relative flex-shrink-0">
                    <div 
                        class="absolute inset-0 rounded-2xl blur-md opacity-50 animate-pulse"
                        :class="{
                            'bg-gradient-to-br from-[#00C853] to-[#00A344]': type === 'success',
                            'bg-gradient-to-br from-[#23378C] to-[#B30034]': type === 'error'
                        }"
                    ></div>
                    <div 
                        class="relative w-12 h-12 max-[639px]:w-10 max-[639px]:h-10 rounded-2xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform"
                        :class="{
                            'bg-gradient-to-br from-[#00C853] via-[#00E676] to-[#69F0AE]': type === 'success',
                            'bg-gradient-to-br from-[#23378C] via-[#FF1744] to-[#F50057]': type === 'error'
                        }"
                    >
                        <svg x-show="type === 'success'" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-md max-[639px]:w-5 max-[639px]:h-5">
                            <path d="M20 6L9 17L4 12" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <svg x-show="type === 'error'" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-md max-[639px]:w-5 max-[639px]:h-5">
                            <path d="M18 6L6 18M6 6L18 18" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
                
                <div class="flex-1 pr-2">
                    <p 
                        class="font-inter text-[11px] max-[639px]:text-[10px] font-semibold uppercase tracking-wider mb-0.5"
                        :class="{
                            'text-[#00C853]': type === 'success',
                            'text-[#23378C]': type === 'error'
                        }"
                        x-text="type === 'success' ? 'Éxito' : 'Error'"
                    ></p>
                    <p class="text-gray-900 font-inter text-[15px] max-[639px]:text-[13px] font-semibold leading-tight" x-text="message"></p>
                </div>
                
                <div class="flex-shrink-0 relative max-[639px]:hidden">
                    <div 
                        class="absolute inset-0 rounded-xl blur-sm"
                        :class="{
                            'bg-[#00C853]/10': type === 'success',
                            'bg-[#23378C]/10': type === 'error'
                        }"
                    ></div>
                    <div 
                        class="relative w-11 h-11 rounded-xl flex items-center justify-center shadow-md transform hover:scale-110 transition-transform"
                        :class="{
                            'bg-gradient-to-br from-[#00C853] to-[#00A344]': type === 'success',
                            'bg-gradient-to-br from-[#23378C] to-[#B30034]': type === 'error'
                        }"
                    >
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 2V8H20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 13H8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 17H8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10 9H9H8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div 
                class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent to-transparent"
                :class="{
                    'via-[#00C853]/30': type === 'success',
                    'via-[#23378C]/30': type === 'error'
                }"
            ></div>
        </div>
    </div>
    @endif

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
        
        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
            opacity: 0;
        }
    </style>
</div>