@forelse($productos as $producto)
<tr class="hover:bg-slate-50 transition">
    <td class="px-4 py-3 text-center text-slate-600 font-medium font-mono uppercase">
        {{ $producto->orden ?? '-' }}
    </td>
    <td class="px-4 py-3 text-center text-slate-700 font-mono text-sm">
        {{ $producto->pk_externa ?? '—' }}
    </td>
    <td class="px-4 py-3 text-center">
        @if($producto->simbolo?->display_entity)
            <span
                class="inline-flex h-7 min-w-7 items-center justify-center rounded-full border px-2 text-base leading-none normal-case {{ $producto->simbolo->display_classes }}"
                title="{{ $producto->simbolo->nombre }}"
                aria-label="{{ $producto->simbolo->nombre }}"
            >
                {!! $producto->simbolo->display_entity !!}
            </span>
        @else
            <span class="text-slate-300">-</span>
        @endif
    </td>
    <td class="px-4 py-3 text-center">
        @php $portada = $producto->gallery->firstWhere('portada', true) ?? $producto->gallery->first(); @endphp
        @if($portada)
            <img src="{{ Storage::url($portada->image) }}" alt="Portada" class="w-12 h-12 object-cover rounded-md border border-slate-200 mx-auto">
        @else
            <div class="w-12 h-12 rounded-md border border-slate-200 bg-slate-100 flex items-center justify-center mx-auto">
                <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        @endif
    </td>
    <td class="px-4 py-3 text-slate-900 font-medium">
        {{ $producto->codigo ?? '-' }}
    </td>
    <td class="px-4 py-3">
        <div class="max-w-xs">
            <p class="text-slate-900 font-medium truncate">{{ $producto->titulo }}</p>
            @if($producto->bulto)
                <p class="text-xs text-slate-500 mt-0.5">Bulto: {{ $producto->bulto }}</p>
            @endif
        </div>
    </td>
    <td class="px-4 py-3">
        <div class="flex flex-wrap gap-1">
            @forelse($producto->categorias as $cat)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    {{ $cat->titulo }}{{ $cat->rubro ? ' - ' . $cat->rubro->titulo : '' }}
                </span>
            @empty
                <span class="text-slate-400 text-sm">Sin categoría</span>
            @endforelse
        </div>
    </td>
    <td class="px-4 py-3 text-center">
        <div class="space-y-0.5">
            @if($producto->precio_unitario)
                <p class="font-semibold text-slate-900">${{ number_format($producto->precio_unitario, 2) }}</p>
            @endif
            @if(!$producto->precio_unitario && !$producto->precio_paquete && !$producto->precio_kg)
                <span class="text-slate-400 text-sm">-</span>
            @endif
        </div>
    </td>
    <td class="px-4 py-3">
        <div class="flex flex-wrap gap-1 justify-center">
            <button class="toggle-visible-btn inline-flex items-center px-2 py-0.5 rounded text-xs font-medium cursor-pointer transition-colors {{ $producto->visible ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}"
                    data-id="{{ $producto->id }}"
                    data-visible="{{ $producto->visible ? '1' : '0' }}"
                    title="Clic para {{ $producto->visible ? 'ocultar' : 'mostrar' }}">
                {{ $producto->visible ? 'Visible' : 'Oculto' }}
            </button>
            @if($producto->destacado)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                    Destacado
                </span>
            @endif
            @if($producto->descuento > 0)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-pink-100 text-pink-800">
                    -{{ $producto->descuento }}%
                </span>
            @endif
        </div>
    </td>
    <td class="px-4 py-3">
        <div class="flex items-center justify-center gap-2">
            <a href="{{ route('productos-todotex.edit', $producto->id) }}?back_page={{ request('page', 1) }}"
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
    <td colspan="10" class="px-4 py-12 text-center text-slate-500">
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
