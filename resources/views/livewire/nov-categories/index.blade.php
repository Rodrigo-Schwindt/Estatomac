@extends('layouts.admin')

@section('content')

<div class="space-y-6 pb-8 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">

    <div class="flex justify-between items-center pb-4">
        <h2 class="text-2xl font-bold text-slate-900">
            Categorías de Novedades (<span id="total-count">{{ $categories->total() }}</span>)
        </h2>

        <a href="{{ route('novcategories.create') }}"
           class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Categoría
        </a>
    </div>

    <div id="alert-success" class="hidden bg-green-100 border border-green-400 text-green-800 px-6 py-3 rounded-md mb-4"></div>

    <div class="text-center text-sm text-slate-600 pb-1 font-medium" id="pagination-info">
        Página {{ $categories->currentPage() }} de {{ $categories->lastPage() }} ·
        Mostrando {{ $categories->firstItem() }}–{{ $categories->lastItem() }} de {{ $categories->total() }}
    </div>

    <div class="bg-white border border-slate-200 rounded-md p-4">
        <div class="relative">
            <input type="text" id="search-input" value="{{ $search }}"
                   placeholder="Buscar por título u orden..."
                   class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-md focus:ring-2 focus:ring-blue-600 focus:outline-none">

            <svg class="absolute top-2.5 left-3 w-5 h-5 text-slate-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6M16 10a6 6 0 11-12 0 6 6 0 0112 0z"/>
            </svg>
        </div>
    </div>

    {{-- AJAX SECTION --}}
    <div id="ajax-wrapper">

        {{-- TABLA --}}
        <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm text-slate-700">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200 text-xs uppercase">
                    <tr>
                        <th class="p-3 text-center font-medium">Orden</th>
                        <th class="p-3 font-medium text-start">Título</th>
                        <th class="p-3 text-center font-medium">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @foreach ($categories as $category)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 text-center font-mono uppercase">{{ $category->orden }}</td>
                            <td class="p-3 font-semibold text-slate-900 uppercase">{{ $category->title }}</td>

                            <td class="p-3 text-center flex items-center justify-center gap-4">
                                <a href="{{ route('novcategories.edit', $category->id) }}"
                                   class="text-slate-500 hover:text-blue-600 transition cursor-pointer">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>

                                <button class="btn-delete text-red-600 hover:text-red-700 transition cursor-pointer"
                                        data-id="{{ $category->id }}">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                                              m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>

                            </td>
                        </tr>
                    @endforeach

                    @if ($categories->count() === 0)
                        <tr>
                            <td colspan="3" class="p-8 text-center italic text-slate-500">
                                No hay categorías disponibles.
                            </td>
                        </tr>
                    @endif

                </tbody>
            </table>
        </div>

        {{-- PAGINACIÓN --}}
        <div class="pt-4 flex justify-center">
            @if ($categories->lastPage() > 1)
                <div class="flex gap-2">
                    @for ($i = 1; $i <= $categories->lastPage(); $i++)
                        <a href="#" data-page="{{ $i }}"
                           class="ajax-page px-3 py-1 border rounded-md text-sm
                                  {{ $i == $categories->currentPage()
                                    ? 'bg-gray-200 text-gray-700'
                                    : 'bg-white text-slate-700 hover:bg-slate-100' }}">
                            {{ $i }}
                        </a>
                    @endfor
                </div>
            @endif
        </div>

    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const searchInput = document.getElementById("search-input");

    searchInput.addEventListener("input", () => loadTable(1));

    document.addEventListener("click", function(e) {
        const pageLink = e.target.closest(".ajax-page");
        if (pageLink) {
            e.preventDefault();
            loadTable(pageLink.dataset.page);
        }
    });

    document.addEventListener("click", function(e) {
        const del = e.target.closest(".btn-delete");
        if (del) {
            const id = del.dataset.id;
            if (confirm("¿Eliminar categoría?")) deleteCategory(id);
        }
    });

    function loadTable(page = 1) {
        const search = searchInput.value;

        fetch(`{{ route('novcategories.index') }}?page=${page}&search=${encodeURIComponent(search)}`, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById("ajax-wrapper").innerHTML = data.html;
            document.getElementById("total-count").innerText = data.total;

            if (data.pages > 0) {
                document.getElementById("pagination-info").innerText =
                    `Página ${page} de ${data.pages} · ${data.total} resultados`;
            } else {
                document.getElementById("pagination-info").innerText =
                    "No hay resultados disponibles";
            }
        });
    }

    function deleteCategory(id) {
        fetch(`{{ url('/admin/novcategorias') }}/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                loadTable();
            }
        });
    }

});
</script>

@endsection
