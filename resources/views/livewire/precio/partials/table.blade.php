@forelse($precios as $precio)
    <tr class="hover:bg-slate-50 transition {{ $precio->vigente_sn ? 'bg-green-50/40' : '' }}">
        <td class="px-4 py-4 font-medium text-slate-900">
            {{ $precio->title }}
            @if($precio->observaciones)
                <p class="text-xs text-slate-500 mt-0.5 truncate max-w-md">{{ $precio->observaciones }}</p>
            @endif
        </td>
        <td class="px-4 py-4 text-center text-slate-700">
            {{ $precio->numero ?? '—' }}
        </td>
        <td class="px-4 py-4 text-center text-slate-500">
            {{ $precio->fecha_desde ? $precio->fecha_desde->format('d/m/Y') : '—' }}
        </td>
        <td class="px-4 py-4 text-center">
            @if($precio->vigente_sn)
                <span class="px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-700">VIGENTE</span>
            @else
                <form action="{{ route('precios.activar', $precio->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('¿Activar esta lista como vigente? La actual quedará desactivada.')"
                            class="px-2 py-1 rounded text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition cursor-pointer">
                        Activar
                    </button>
                </form>
            @endif
        </td>
        <td class="px-4 py-4 text-center">
            @php $ext = pathinfo($precio->archivo, PATHINFO_EXTENSION); @endphp
            <span class="px-2 py-1 rounded text-xs font-bold {{ $ext == 'pdf' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                {{ strtoupper($ext) }}
            </span>
        </td>
        <td class="text-center">
            <div class="flex items-center justify-center gap-3">
                <a href="{{ Storage::url($precio->archivo) }}" target="_blank" class="text-slate-400 hover:text-blue-600 transition" title="Ver archivo">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                <a href="{{ route('precios.edit', $precio->id) }}" class="text-slate-400 hover:text-blue-600 transition" title="Editar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </a>
                <button class="delete-btn text-slate-400 hover:text-red-600 transition" data-id="{{ $precio->id }}" title="Eliminar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr><td colspan="6" class="px-6 py-10 text-center text-slate-500">No hay registros</td></tr>
@endforelse
