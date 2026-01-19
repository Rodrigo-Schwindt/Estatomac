@forelse($equivalencias as $equivalencia)
<tr class="hover:bg-slate-50 transition">
    <td class="px-4 py-3 text-center">
        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium bg-slate-100 text-slate-700 rounded">
            {{ $equivalencia->order ?: 'N/A' }}
        </span>
    </td>
    <td class="px-4 py-3">
        <div class="font-medium text-slate-900">{{ $equivalencia->title }}</div>
    </td>
    <td class="px-4 py-3">
        <div class="text-slate-700">{{ $equivalencia->code ?: '-' }}</div>
    </td>
    <td class="px-4 py-3">
        <div class="text-sm text-slate-700">{{ $equivalencia->producto->title ?? 'N/A' }}</div>
    </td>
    <td class="px-4 py-3">
        <div class="flex items-center justify-center gap-2">
            <a href="{{ route('equivalencias.edit', $equivalencia) }}" 
               class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
               title="Editar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <button type="button" 
                    class="delete-btn inline-flex items-center justify-center w-8 h-8 bg-red-600 text-white rounded hover:bg-red-700 transition"
                    data-id="{{ $equivalencia->id }}"
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
    <td colspan="5" class="px-4 py-8 text-center text-slate-500">
        No se encontraron equivalencias
    </td>
</tr>
@endforelse