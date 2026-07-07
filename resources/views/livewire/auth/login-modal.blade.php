<div x-data="{ open: @entangle('open'), isLogin: true }">
    <a @click.prevent="open = true; isLogin = true"
       class="nav-link text-[#23378C] cursor-pointer rounded-[40px] font-inter text-[16px] font-normal w-[155px] h-[40px] rounded-[4px] border border-[#23378C] flex justify-center items-center text-center relative hover:bg-black hover:text-white transition-colors">
        Zona Privada
    </a>

    <div x-show="open" 
         x-transition.opacity 
         @click="open = false" 
         class="fixed inset-0 bg-black/60 z-[100]" 
         x-cloak></div>

    <div x-show="open" 
         x-transition 
         class="fixed inset-0 z-[101] flex items-center justify-center px-4" 
         x-cloak>
        <div class="bg-white w-full max-w-[540px] rounded-xl p-8 shadow-2xl max-h-[90vh] overflow-y-auto" @click.away="open = false">
            
            <h2 class="text-[28px] text-[#1b1b18] font-inter font-semibold text-center mb-6"
                x-text="isLogin ? 'Zona Privada' : 'Crear cuenta'"></h2>

            <form x-show="isLogin" wire:submit.prevent="login" class="space-y-4">
                <div>
                    <label class="text-[14px] text-gray-700">Nombre de usuario o correo electrónico</label>
                    <input type="text" wire:model.defer="username" 
                           class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                    @error('username') <span class="text-red-500 text-[12px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-[14px] text-gray-700">Contraseña</label>
                    <input type="password" wire:model.defer="password" 
                           class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                </div>

                <div class="pt-2 space-y-3">
                    <button type="submit"
                            class="w-full h-[52px] bg-[#23378C] text-white rounded-lg font-medium text-[14px] cursor-pointer transition-colors">
                        INICIAR SESIÓN
                    </button>
                    <button type="button" @click="isLogin = false"
                            class="w-full h-[52px] bg-white text-[#23378C] border-1 border-[#23378C] rounded-lg font-medium text-[14px] cursor-pointer transition-colors">
                        REGISTRARME
                    </button>
                </div>

                <div class="text-center pt-2">
                    <a href="{{ route('cliente.password.request') }}" class="text-[13px] text-[#23378C] hover:underline">
                        Olvidé mi password
                    </a>
                </div>

                <p class="text-center text-[12px] text-gray-400 pt-1">
                    ¿Necesitás ayuda? Contactá a <x-soporte-ventas/>
                </p>
            </form>

            <!-- REGISTER FORM -->
            <form x-show="!isLogin" wire:submit.prevent="register" class="space-y-4" x-cloak>
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

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[14px] text-gray-700">Contraseña *</label>
                        <input type="password" wire:model.defer="reg_password" 
                               class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                    </div>
                    <div>
                        <label class="text-[14px] text-gray-700">Confirmar *</label>
                        <input type="password" wire:model.defer="reg_password_confirmation" 
                               class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4 focus:ring-0 focus:border-gray-400">
                    </div>
                </div>
                @error('reg_password') <span class="text-red-500 text-[12px]">{{ $message }}</span> @enderror

                <div class="pt-2 space-y-3">
                    <button type="button" @click="isLogin = true"
                            class="w-full h-[52px] bg-[#23378C] text-white rounded-lg font-medium text-[14px] cursor-pointer transition-colors">
                        INICIAR SESIÓN
                    </button>
                    <button type="submit" 
                            class="w-full h-[52px] bg-white text-[#23378C] border-1 border-[#23378C] rounded-lg font-medium text-[14px] cursor-pointer transition-colors">
                        REGISTRARME
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>