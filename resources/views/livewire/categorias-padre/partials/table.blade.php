@forelse($categoriasPadre as $index => $categoriaPadre)
<tr class="hover:bg-slate-50 transition">
    <td class="px-4 py-3 text-center">
        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium bg-slate-100 text-slate-700 rounded">
            {{ $categoriaPadre->order ?: 'N/A' }}
        </span>
    </td>
    <td class="px-4 py-3">
        <div class="font-medium text-slate-900">{{ $categoriaPadre->title }}</div>
    </td>
    <td class="px-4 py-3 text-center">
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            {{ $categoriaPadre->categorias->count() }}
        </span>
    </td>
    <td class="px-4 py-3">
        <div class="flex items-center justify-center gap-2">
            <a href="{{ route('categorias-padre.edit', $categoriaPadre) }}" 
               class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-xs">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            @if($index < 2)
                <button type="button" 
                        class="protected-delete-btn inline-flex items-center px-3 py-1.5 bg-slate-400 text-white rounded-md cursor-not-allowed transition text-xs"
                        title="Esta categoría padre no se puede eliminar">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Protegido
                </button>
            @else
                <button type="button" 
                        class="delete-btn inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition text-xs"
                        data-id="{{ $categoriaPadre->id }}">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar
                </button>
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="px-4 py-8 text-center text-slate-500">
        No se encontraron categorías padre
    </td>
</tr>
@endforelse