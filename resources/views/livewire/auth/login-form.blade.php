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
        @error('password')
            <span class="text-red-500 text-[12px]">{{ $message }}</span>
        @enderror
    </div>

    <button
        type="submit"
        class="w-full h-[52px] bg-[#BA2025] text-white rounded-lg
               font-medium text-[14px] hover:bg-[#8B1419] transition-colors"
    >
        INICIAR SESIÓN
    </button>
</form>