<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" data-sort="nombre">
                Nombre
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" data-sort="email">
                Email
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Teléfono / Celular
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" data-sort="comision">
                Comisión %
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Estado
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Acciones
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($vendedores as $vendedor)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $vendedor->nombre }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $vendedor->email ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $vendedor->telefono ?? $vendedor->celular ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $vendedor->comision }}%
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="relative inline-block">
                        <input
                            type="checkbox"
                            id="toggle-{{ $vendedor->id }}"
                            {{ $vendedor->activo ? 'checked' : '' }}
                            onchange="toggleActivo({{ $vendedor->id }})"
                            class="sr-only peer"
                        >
                        <label
                            for="toggle-{{ $vendedor->id }}"
                            class="block w-11 h-6 bg-gray-300 rounded-full cursor-pointer peer-checked:bg-green-500 transition-colors duration-300 relative"
                        >
                            <span class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-300 peer-checked:translate-x-5"></span>
                        </label>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('vendedores.edit', $vendedor) }}" class="text-blue-600 hover:text-blue-900 mr-3 cursor-pointer">
                        Editar
                    </a>
                    <button onclick="resetPassword({{ $vendedor->id }})" class="text-amber-600 hover:text-amber-800 mr-3 cursor-pointer" title="Blanquear contraseña">
                        Reset password
                    </button>
                    <button onclick="deleteVendedor({{ $vendedor->id }})" class="text-red-600 hover:text-red-900 cursor-pointer">
                        Eliminar
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                    No se encontraron vendedores
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<style>
@foreach($vendedores as $vendedor)
#toggle-{{ $vendedor->id }}:checked + label span {
    transform: translateX(1.25rem);
}
@endforeach
</style>
