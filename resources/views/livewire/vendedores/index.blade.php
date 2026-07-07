@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Vendedores</h1>
        <a href="{{ route('vendedores.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Nuevo Vendedor
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4">
            <input
                type="text"
                id="searchInput"
                placeholder="Buscar por nombre, email o teléfono..."
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </div>

        <div id="tableContainer">
            @include('livewire.vendedores.partials.table')
        </div>

        <div id="paginationContainer" class="p-4">
            @include('livewire.vendedores.partials.pagination')
        </div>
    </div>
</div>

<script>
let searchTimeout;
let currentPage = 1;
let currentSort = { field: 'nombre', direction: 'asc' };

document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentPage = 1;
        loadData();
    }, 500);
});

function loadData() {
    const search = document.getElementById('searchInput').value;
    const url = `{{ route('vendedores.index') }}?page=${currentPage}&search=${search}&sortField=${currentSort.field}&sortDirection=${currentSort.direction}`;

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('tableContainer').innerHTML = data.html;
        document.getElementById('paginationContainer').innerHTML = data.pagination;
        attachSortListeners();
    });
}

function sortBy(field) {
    if (currentSort.field === field) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.field = field;
        currentSort.direction = 'asc';
    }
    loadData();
}

function goToPage(page) {
    currentPage = page;
    loadData();
}

function attachSortListeners() {
    document.querySelectorAll('[data-sort]').forEach(el => {
        el.addEventListener('click', () => sortBy(el.dataset.sort));
    });
}

function deleteVendedor(id) {
    if (confirm('¿Estás seguro de eliminar este vendedor? Los clientes asignados quedarán sin vendedor.')) {
        fetch(`/admin/vendedores/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) loadData();
        });
    }
}

function toggleActivo(id) {
    fetch(`/admin/vendedores/${id}/toggle-activo`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) loadData();
    });
}

function resetPassword(id) {
    if (!confirm('¿Blanquear la contraseña? El vendedor deberá definirla en su próximo ingreso.')) return;
    fetch(`/admin/vendedores/${id}/reset-password`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) alert(data.message);
    });
}

attachSortListeners();
</script>
@endsection
