<div x-data="{ open: @entangle('open') }">
    <div x-show="open" 
         x-transition.opacity 
         @click="open = false" 
         class="fixed inset-0 bg-black/60 z-[100]" 
         x-cloak></div>

    <div x-show="open" 
         x-transition 
         class="fixed inset-0 z-[101] flex items-center justify-center px-4" 
         x-cloak>
        <div class="bg-white w-full max-w-[640px] rounded-xl p-8 shadow-2xl max-h-[90vh] overflow-y-auto relative" @click.away="open = false">
            
            <button 
                @click="open = false"
                class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors cursor-pointer z-10"
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <h2 class="text-[28px] text-[#1b1b18] font-inter font-semibold text-center mb-6">
                Mis Datos
            </h2>

            <form wire:submit.prevent="actualizar" class="space-y-4">
                <div>
                    <label class="text-[14px] text-gray-700">Nombre completo *</label>
                    <input type="text" wire:model.defer="nombre" 
                           class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                    @error('nombre') <span class="text-red-500 text-[12px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-[14px] text-gray-700">Correo electrónico *</label>
                    <input type="email" wire:model.defer="email" 
                           class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                    @error('email') <span class="text-red-500 text-[12px]">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[14px] text-gray-700">CUIL</label>
                        <input type="text" wire:model.defer="cuil" 
                               class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                        @error('cuil') <span class="text-red-500 text-[12px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-[14px] text-gray-700">CUIT</label>
                        <input type="text" wire:model.defer="cuit" 
                               class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                        @error('cuit') <span class="text-red-500 text-[12px]">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="text-[14px] text-gray-700">Teléfono</label>
                    <input type="text" wire:model.defer="telefono" 
                           class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                    @error('telefono') <span class="text-red-500 text-[12px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-[14px] text-gray-700">Domicilio</label>
                    <input type="text" wire:model.defer="domicilio" 
                           class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                    @error('domicilio') <span class="text-red-500 text-[12px]">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[14px] text-gray-700">Localidad</label>
                        <input type="text" wire:model.defer="localidad" 
                               class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                        @error('localidad') <span class="text-red-500 text-[12px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-[14px] text-gray-700">Provincia</label>
                        <input type="text" wire:model.defer="provincia" 
                               class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                        @error('provincia') <span class="text-red-500 text-[12px]">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 mt-6">
                    <h3 class="text-[18px] text-[#1b1b18] font-inter font-semibold mb-4">
                        Cambiar Contraseña (opcional)
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[14px] text-gray-700">Contraseña Actual</label>
                            <input type="password" wire:model.defer="password_actual" 
                                   class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                            @error('password_actual') <span class="text-red-500 text-[12px]">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[14px] text-gray-700">Nueva Contraseña</label>
                                <input type="password" wire:model.defer="nueva_password" 
                                       class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                            </div>
                            <div>
                                <label class="text-[14px] text-gray-700">Confirmar</label>
                                <input type="password" wire:model.defer="nueva_password_confirmation" 
                                       class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                            </div>
                        </div>
                        @error('nueva_password') <span class="text-red-500 text-[12px]">{{ $message }}</span> @enderror
                        <p class="text-[12px] text-gray-500">Deja estos campos en blanco si no deseas cambiar tu contraseña</p>
                    </div>
                </div>

                <div class="pt-4 space-y-3">
                    <button type="submit" 
                            class="w-full h-[52px] bg-[#23378C] text-white rounded-lg font-medium text-[14px] cursor-pointer transition-colors hover:bg-[#B30034]">
                        GUARDAR CAMBIOS
                    </button>
                    <button type="button" @click="open = false"
                            class="w-full h-[52px] bg-white text-gray-700 border border-gray-300 rounded-lg font-medium text-[14px] cursor-pointer transition-colors hover:bg-gray-50">
                        CANCELAR
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>