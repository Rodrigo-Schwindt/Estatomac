@forelse($rubros as $rubro)
    <tr class="hover:bg-slate-50 transition">
        <td class="px-4 py-4 text-center font-semibold text-slate-900">{{ $rubro->orden ?? '-' }}</td>

        <td class="px-4 py-4">
            <p class="font-medium text-slate-900">{{ $rubro->titulo }}</p>
        </td>

        <td class="px-4 py-4 text-center">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                {{ $rubro->categorias_count }}
            </span>
        </td>

        <td class="text-center">
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('rubros.orden', $rubro->id) }}"
                   title="Ordenar productos"
                   class="text-slate-500 hover:text-violet-600 transition cursor-pointer">
                    <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
                    </svg>
                </a>
                <a href="{{ route('rubros.edit', $rubro->id) }}"
                   class="text-slate-500 hover:text-blue-600 transition cursor-pointer">
                    <svg class="w-[24px] h-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </a>

                <button class="delete-btn text-red-500 hover:text-red-600 transition cursor-pointer"
                        data-id="{{ $rubro->id }}">
                    <svg class="w-[28px] h-[28px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="px-6 py-10 text-center text-slate-500 text-sm">
            No hay rubros disponibles
        </td>
    </tr>
@endforelse
