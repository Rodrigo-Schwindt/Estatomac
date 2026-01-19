@forelse($productos as $producto)
<tr class="hover:bg-slate-50 transition">
    <td class="px-4 py-3 text-center text-slate-600 font-medium">
        {{ $producto->order ?? '-' }}
    </td>
    <td class="px-4 py-3 text-slate-900 font-medium">
        {{ $producto->code ?? '-' }}
    </td>
    <td class="px-4 py-3">
        <div class="flex items-center gap-3">
            @if($producto->image)
                <img src="{{ asset('storage/' . $producto->image) }}" 
                     alt="{{ $producto->title }}" 
                     class="w-12 h-12 object-cover rounded border border-slate-200">
            @else
                <div class="w-12 h-12 bg-slate-100 rounded border border-slate-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
            <div class="max-w-md">
                <p class="text-slate-900 font-medium truncate">{{ $producto->title }}</p>
            </div>
        </div>
    </td>
    <td class="px-4 py-3">
        @if($producto->categoria)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                {{ $producto->categoria->title }}
            </span>
        @else
            <span class="text-slate-400 text-sm">Sin categoría</span>
        @endif
    </td>
    <td class="px-4 py-3 text-center">
        <div class="space-y-1">
            <p class="font-semibold text-slate-900">${{ number_format($producto->precio, 2) }}</p>
            @if($producto->descuento > 0)
                <p class="text-xs text-red-600">-${{ number_format($producto->descuento, 2) }}</p>
            @endif
        </div>
    </td>
    <td class="px-4 py-3">
        <div class="flex flex-wrap gap-1 justify-center">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $producto->visible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $producto->visible ? 'Activo' : 'Inactivo' }}
            </span>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $producto->nuevo === 'nuevo' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                {{ ucfirst($producto->nuevo) }}
            </span>
            @if($producto->importados)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                    Importado
                </span>
            @endif
            @if($producto->destacado)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                    ⭐ Destacado
                </span>
            @endif
            @if($producto->oferta)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-pink-100 text-pink-800">
                    🏷️ Oferta
                </span>
            @endif
        </div>
    </td>
    <td class="px-4 py-3">
        <div class="flex items-center justify-center gap-2">
            <a href="{{ route('productos.edit', $producto->id) }}" 
               class="p-2 text-blue-600 hover:bg-blue-50 rounded transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <button class="delete-btn p-2 text-red-600 hover:bg-red-50 rounded transition" 
                    data-id="{{ $producto->id }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="px-4 py-12 text-center text-slate-500">
        <div class="flex flex-col items-center gap-2">
            <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="text-lg font-medium">No se encontraron productos</p>
            <p class="text-sm">Intenta ajustar los filtros de búsqueda</p>
        </div>
    </td>
</tr>
@endforelse