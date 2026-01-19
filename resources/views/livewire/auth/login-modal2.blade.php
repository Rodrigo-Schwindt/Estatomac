<div x-data="{ open: @entangle('open') }">
    <a
    
        @click.prevent="open = true"
        class="nav-link text-white cursor-pointer font-inter text-[14px] font-normal
               w-[164px] h-[44px] rounded-[4px] border border-white
               flex justify-center items-center text-center relative
               hover:bg-white hover:text-black transition-colors"
    >
        ACCESO CLIENTES
    </a>

    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition.opacity
        @click="open = false"
        class="fixed inset-0 bg-black/60 z-[100]"
    ></div>

    {{-- Modal --}}
    <div
        x-show="open"
        x-transition
        class="fixed inset-0 z-[101] flex items-center justify-center px-4"
    >
        <div class="bg-white w-full max-w-[420px] rounded-xl p-8">
            <h2 class="text-[28px] text-[#1b1b18] font-inter font-semibold text-center mb-6">
                Acceso clientes
            </h2>

            <form wire:submit.prevent="login" class="space-y-4">
                <div>
                    <label class="text-[14px] text-gray-700">
                        Nombre de usuario o correo electrónico
                    </label>
                    <input
                        type="text"
                        wire:model.defer="username"
                        class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4
                               focus:ring-0 focus:border-gray-400"
                    >
                    @error('username')
                        <span class="text-red-500 text-[12px]">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="text-[14px] text-gray-700">
                        Contraseña
                    </label>
                    <input
                        type="password"
                        wire:model.defer="password"
                        class="w-full mt-1 h-[52px] rounded-lg border border-gray-300 px-4
                               focus:ring-0 focus:border-gray-400"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full h-[52px] bg-[#BA2025] text-white rounded-lg
                           font-medium text-[14px] hover:bg-[#8B1419] transition-colors"
                >
                    INICIAR SESIÓN
                </button>

            </form>
        </div>
    </div>
</div>