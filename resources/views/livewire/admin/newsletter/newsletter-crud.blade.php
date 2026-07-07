@extends('layouts.admin')

@section('content')

<div class="space-y-10 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">
    <div class="flex justify-between items-center pb-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Newsletter</h2>
            <p class="text-slate-600 text-sm">Gestiona los suscriptores y envía newsletters</p>
        </div>

        @if (!request()->has('showMessageForm'))
        <div class="flex items-center gap-3">

            <form method="GET" class="relative">
                <input 
                    type="text"
                    name="searchTerm"
                    value="{{ $searchTerm }}"
                    placeholder="Buscar por email o ID..."
                    class="border border-slate-300 rounded-md px-3 py-2 text-sm pl-9 w-64 focus:ring-2 focus:ring-blue-600"
                >
                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>

                @if($searchTerm)
                    <a href="{{ route('admin.newsletter') }}" 
                       class="absolute right-3 top-1 text-xl text-slate-500 hover:text-red-500">×</a>
                @endif
            </form>

            <a href="{{ route('admin.newsletter', ['showMessageForm' => 1]) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 cursor-pointer">
                Enviar Newsletter
            </a>

        </div>
        @endif
    </div>

    @if(request()->has('showMessageForm'))

    <form action="{{ route('admin.newsletter.send') }}" 
          method="POST" 
          enctype="multipart/form-data"
          class="bg-white border border-slate-200 rounded-md shadow-sm p-6 space-y-6">

        @csrf

        <h3 class="text-xl font-semibold text-slate-800 pb-1">Enviar Newsletter</h3>

        <div>
            <label class="text-sm font-medium text-slate-700">Asunto *</label>
            <input type="text"
                   name="subject"
                   class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600"
                   value="{{ old('subject') }}"
                   placeholder="Ingresá el asunto del newsletter">
            @error('subject') <p class="text-red-600 text-xs">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="text-sm font-medium text-slate-700">Mensaje *</label>
            <textarea name="message"
                      rows="6"
                      class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-600"
                      placeholder="Escribí el contenido del newsletter...">{{ old('message') }}</textarea>
            @error('message') <p class="text-red-600 text-xs">{{ $message }}</p> @enderror
        </div>

        <p class="text-sm text-slate-500">
            Se enviará a <strong>{{ $activeSubscribersCount }}</strong> suscriptores activos.
        </p>

        <div class="pt-4 flex justify-end gap-3">
            <a href="{{ route('admin.newsletter') }}" 
               class="px-4 py-2 border border-slate-300 rounded-md text-sm hover:bg-slate-100 cursor-pointer">
                Cancelar
            </a>

            <button class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 cursor-pointer">
                Enviar Newsletter
            </button>
        </div>

    </form>

    @endif

    <div class="bg-white border border-slate-200 rounded-md shadow-sm p-6">

        @if($searchTerm)
            <p class="text-sm text-slate-600 pb-4">
                {{ $subscribers->total() }} resultado(s) para "{{ $searchTerm }}"
            </p>
        @endif

        <p class="text-sm text-slate-600 pb-4">
            Página {{ $subscribers->currentPage() }} de {{ $subscribers->lastPage() }} |
            Mostrando {{ $subscribers->firstItem() }}–{{ $subscribers->lastItem() }} de {{ $subscribers->total() }}
        </p>

        @if($subscribers->count() === 0)

            <p class="text-slate-600 text-sm text-center py-10">
                @if($searchTerm)
                    No se encontraron suscriptores para "{{ $searchTerm }}"
                @else
                    No hay suscriptores registrados
                @endif
            </p>

        @else

            <div class="overflow-hidden rounded-md border border-slate-200">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 border-b border-slate-200 text-xs uppercase">
                        <tr>
                            <th class="p-3 text-left">ID</th>
                            <th class="p-3 text-left">Email</th>
                            <th class="p-3 text-center">Estado</th>
                            <th class="p-3 text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">

                        @foreach($subscribers as $sub)
                        <tr>
                            <td class="p-3">{{ $sub->id }}</td>
                            <td class="p-3">{{ $sub->email }}</td>

                            <td class="p-3 text-center">
                                <form action="{{ route('admin.newsletter.toggle', $sub->id) }}" method="POST">
                                    @csrf
                                    <label class="switch relative inline-block w-12 h-6 cursor-pointer">
                                        <input type="checkbox"
                                               onchange="this.form.submit()"
                                               class="sr-only"
                                               {{ $sub->active ? 'checked' : '' }}>
                                        <span class="slider absolute inset-0 bg-slate-300 rounded-full transition"></span>
                                    </label>
                                </form>
                            </td>

                            <td class="p-3 text-center">
                                <form action="{{ route('admin.newsletter.delete', $sub->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline cursor-pointer">
                                        <svg class="w-[28px] h-[28px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                {{ $subscribers->links() }}
            </div>

        @endif

    </div>

</div>
<style>
    .switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 22px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.switch .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: .3s;
    border-radius: 9999px;
}

.switch .slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .3s;
    border-radius: 9999px;
}

/* Estado ON */
.switch input:checked + .slider {
    background-color: royalblue; /* azul indigo-600 */
}

.switch input:checked + .slider:before {
    transform: translateX(22px);
}

</style>
@endsection
