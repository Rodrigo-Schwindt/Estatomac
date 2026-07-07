<div class="max-w-[480px] mx-auto px-4 py-[60px]"
     x-data="{ show: false }"
     x-init="setTimeout(() => show = true, 50)"
     x-show="show"
     x-transition:enter="transition ease-out duration-400"
     x-transition:enter-start="opacity-0 -translate-y-3"
     x-transition:enter-end="opacity-100 translate-y-0">

    @if($forzado)
    <div class="mb-6 bg-amber-50 border border-amber-200 rounded-[10px] px-5 py-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div>
            <p class="text-amber-800 font-inter text-[14px] font-semibold">Tu contraseña expiró</p>
            <p class="text-amber-700 font-inter text-[13px] mt-0.5">Necesitás establecer una nueva contraseña para continuar.</p>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-[12px] shadow-sm border border-gray-100 p-8">
        <h1 class="text-[22px] font-inter font-bold text-gray-900 mb-6">Cambiar contraseña</h1>

        <form wire:submit="cambiar" class="space-y-5">

            <div>
                <label class="block text-[13px] font-inter font-medium text-gray-700 mb-1.5">
                    Contraseña actual
                </label>
                <input
                    type="password"
                    wire:model="password_actual"
                    autocomplete="current-password"
                    class="w-full px-4 py-2.5 border rounded-[8px] font-inter text-[14px] focus:outline-none focus:ring-2 focus:ring-[#23378C] @error('password_actual') border-red-400 @enderror"
                    placeholder="••••••••"
                >
                @error('password_actual')
                    <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[13px] font-inter font-medium text-gray-700 mb-1.5">
                    Nueva contraseña
                </label>
                <input
                    type="password"
                    wire:model="password_nueva"
                    autocomplete="new-password"
                    class="w-full px-4 py-2.5 border rounded-[8px] font-inter text-[14px] focus:outline-none focus:ring-2 focus:ring-[#23378C] @error('password_nueva') border-red-400 @enderror"
                    placeholder="••••••••"
                >
                @error('password_nueva')
                    <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[13px] font-inter font-medium text-gray-700 mb-1.5">
                    Confirmar nueva contraseña
                </label>
                <input
                    type="password"
                    wire:model="password_confirmar"
                    autocomplete="new-password"
                    class="w-full px-4 py-2.5 border rounded-[8px] font-inter text-[14px] focus:outline-none focus:ring-2 focus:ring-[#23378C] @error('password_confirmar') border-red-400 @enderror"
                    placeholder="••••••••"
                >
                @error('password_confirmar')
                    <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="flex-1 bg-[#23378C] text-white font-inter font-semibold text-[15px] h-[44px] rounded-[40px] hover:bg-[#1b2d72] transition-colors disabled:opacity-60"
                >
                    <span wire:loading.remove>Guardar contraseña</span>
                    <span wire:loading>Guardando...</span>
                </button>

                @if(!$forzado)
                <a wire:navigate href="{{ route('cliente.productos') }}"
                   class="text-[13px] font-inter text-gray-500 hover:text-gray-700 whitespace-nowrap">
                    Cancelar
                </a>
                @endif
            </div>

        </form>
    </div>
</div>
