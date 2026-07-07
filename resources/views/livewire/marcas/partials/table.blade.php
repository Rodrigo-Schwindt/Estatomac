@forelse($marcas as $marca)
<tr class="hover:bg-slate-50 transition">
    <td class="px-4 py-3 text-center">
        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium bg-slate-100 text-slate-700 rounded">
            {{ $marca->order ?: 'N/A' }}
        </span>
    </td>
    <td class="px-4 py-3">
        <div class="font-medium text-slate-900">{{ $marca->title }}</div>
    </td>
    <td class="px-4 py-3 text-center">
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            {{ $marca->modelos->count() }}
        </span>
    </td>
    <td class="px-4 py-3">
        <div class="flex items-center justify-center gap-2">
            <a href="{{ route('marcas.edit', $marca) }}" 
               class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
               title="Editar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <button type="button" 
                    class="delete-btn inline-flex items-center justify-center w-8 h-8 bg-red-600 text-white rounded hover:bg-red-700 transition"
                    data-id="{{ $marca->id }}"
                    title="Eliminar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="px-4 py-8 text-center text-slate-500">
        No se encontraron marcas
    </td>
</tr>
@endforelse