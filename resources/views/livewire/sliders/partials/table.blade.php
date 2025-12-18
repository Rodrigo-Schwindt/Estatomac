@forelse($sliders as $slider)
    <tr class="hover:bg-slate-50 transition">
        <td class="px-4 py-4 text-center font-semibold text-slate-900">{{ $slider->orden }}</td>

        <td class="px-4 py-4">
            <div class="w-24 h-16 mx-auto rounded-md overflow-hidden bg-slate-200 flex items-center justify-center">
                @php
                    $ext = strtolower(pathinfo($slider->image, PATHINFO_EXTENSION));
                    $isVideo = in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'avi']);
                @endphp
                
                @if($isVideo)
                    <svg class="w-8 h-8 text-slate-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                @else
                    <img src="{{ Storage::url($slider->image) }}" class="w-full h-full object-cover" alt="{{ $slider->title }}">
                @endif
            </div>
        </td>

        <td class="px-4 py-4">
            <p class="font-medium text-slate-900">{{ $slider->title }}</p>
            <p class="text-slate-600 line-clamp-2">{{ strip_tags($slider->description ?? 'Sin descripción') }}</p>
        </td>

        <td class="text-center">
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('sliders.edit', $slider->id) }}"
                   class="text-slate-500 hover:text-blue-600 transition cursor-pointer">
                    <svg class="w-[24px] h-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </a>

                <button class="delete-btn text-red-500 hover:text-red-600 transition cursor-pointer"
                        data-id="{{ $slider->id }}">
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
            No hay sliders disponibles
        </td>
    </tr>
@endforelse