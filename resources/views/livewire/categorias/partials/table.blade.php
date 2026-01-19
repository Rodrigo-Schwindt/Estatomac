@forelse($categorias as $categoria)
<tr class="hover:bg-slate-50 transition">
    <td class="px-4 py-3 text-center">
        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium bg-slate-100 text-slate-700 rounded">
            {{ $categoria->order ?: 'N/A' }}
        </span>
    </td>
    <td class="px-4 py-3">
        @if($categoria->image)
            <img src="{{ asset('storage/' . $categoria->image) }}" 
                 alt="{{ $categoria->title }}" 
                 class="w-16 h-16 object-cover rounded-md shadow-sm">
        @else
            <div class="w-16 h-16 bg-slate-100 rounded-md flex items-center justify-center">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif
    </td>
    <td class="px-4 py-3">
        <div class="font-medium text-slate-900">{{ $categoria->title }}</div>
    </td>
    <td class="px-4 py-3 text-center">
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            {{ $categoria->subcategorias->count() }}
        </span>
    </td>
    <td class="px-4 py-3">
        <div class="flex items-center justify-center gap-2">
            <a href="{{ route('categorias.edit', $categoria) }}" 
               class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-xs">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            <button type="button" 
                    class="delete-btn inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition text-xs"
                    data-id="{{ $categoria->id }}">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Eliminar
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="px-4 py-8 text-center text-slate-500">
        No se encontraron categorías
    </td>
</tr>
@endforelse