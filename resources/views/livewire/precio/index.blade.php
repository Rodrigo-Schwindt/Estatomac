@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fadeIn bg-white border border-slate-200 rounded-md shadow-sm p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-2xl font-semibold text-slate-900">Listas de Precios (<span id="totalCount">{{ $precios->total() }}</span>)</h2>
        <a href="{{ route('precios.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Subir Lista
        </a>
    </div>

    <div id="alertContainer"></div>

    <div class="bg-white border border-slate-200 rounded-md p-4 shadow-sm">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" id="searchInput" placeholder="Buscar por título..." class="w-full pl-10 pr-10 py-2.5 border border-slate-300 rounded-md bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm">
        </div>
    </div>

    <div class="overflow-x-auto border border-slate-200 rounded-md bg-white shadow-sm">
        <table class="w-full text-sm text-slate-700">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 font-medium text-start sort-header cursor-pointer" data-sort="title">Título</th>
                    <th class="px-4 py-3 text-center font-medium">Formato</th>
                    <th class="px-4 py-3 text-center font-medium sort-header cursor-pointer" data-sort="created_at">Fecha</th>
                    <th class="px-4 py-3 text-center font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody id="preciosTable" class="divide-y divide-slate-200">
                @include('livewire.precio.partials.table', ['precios' => $precios])
            </tbody>
        </table>
    </div>

    <div id="pagination">
        @include('livewire.precio.partials.pagination', ['precios' => $precios])
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadPrecios, 300);
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            const btn = e.target.closest('.delete-btn');
            if (confirm('¿Eliminar esta lista de precios?')) {
                fetch(`/admin/precios/${btn.dataset.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) { loadPrecios(); showAlert(data.message, 'success'); }
                });
            }
        }

        if (e.target.closest('.pagination-link')) {
            e.preventDefault();
            const url = e.target.closest('a').href;
            const page = new URL(url).searchParams.get('page');
            loadPrecios(page);
        }
    });

    function loadPrecios(page = 1) {
        const search = searchInput.value;
        fetch(`{{ route('precios.index') }}?page=${page}&search=${search}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('preciosTable').innerHTML = data.html;
            document.getElementById('pagination').innerHTML = data.pagination;
        });
    }

    function showAlert(msg, type) {
        const container = document.getElementById('alertContainer');
        const color = type === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200';
        container.innerHTML = `<div class="px-4 py-3 rounded-md border ${color} text-sm">${msg}</div>`;
        setTimeout(() => container.innerHTML = '', 3000);
    }
});
</script>
@endsection