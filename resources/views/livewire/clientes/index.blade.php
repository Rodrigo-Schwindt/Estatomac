{{-- resources/views/livewire/clientes/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Clientes</h1>
        <a href="{{ route('clientes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Nuevo Cliente
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <input 
                type="text" 
                id="searchInput"
                placeholder="Buscar por nombre, usuario, email, CUIT o CUIL..." 
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </div>

        <div id="tableContainer">
            @include('livewire.clientes.partials.table')
        </div>

        <div id="paginationContainer" class="p-4 border-t">
            @include('livewire.clientes.partials.pagination')
        </div>
    </div>
</div>

<script>
let searchTimeout;
let currentPage = 1;
let currentSort = { field: 'nombre', direction: 'asc' };

document.getElementById('searchInput').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentPage = 1;
        loadData();
    }, 500);
});

function loadData() {
    const search = document.getElementById('searchInput').value;
    const url = `{{ route('clientes.index') }}?page=${currentPage}&search=${search}&sortField=${currentSort.field}&sortDirection=${currentSort.direction}`;
    
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

function deleteCliente(id) {
    if (confirm('¿Estás seguro de eliminar este cliente?')) {
        fetch(`/admin/clientes/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadData();
            }
        });
    }
}

function toggleActivo(id) {
    fetch(`/admin/clientes/${id}/toggle-activo`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadData();
        }
    });
}

attachSortListeners();
</script>
@endsection