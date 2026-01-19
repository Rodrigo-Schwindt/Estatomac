@extends('layouts.admin')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Gestión de Pedidos</h1>
        
        <div class="flex gap-4">
            <!-- Búsqueda -->
            <form method="GET" action="{{ route('admin.pedidos.index') }}" class="relative">
                <input 
                    type="text" 
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Buscar por nº pedido o cliente..."
                    class="w-80 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <button type="submit" class="absolute right-3 top-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
                
                <!-- Mantener filtro en búsqueda -->
                @if(request('filtro'))
                <input type="hidden" name="filtro" value="{{ request('filtro') }}">
                @endif
            </form>

            <!-- Filtro -->
            <form method="GET" action="{{ route('admin.pedidos.index') }}" id="filtroForm">
                <select 
                    name="filtro"
                    onchange="document.getElementById('filtroForm').submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="todos" {{ request('filtro') === 'todos' ? 'selected' : '' }}>Todos</option>
                    <option value="pendientes" {{ request('filtro') === 'pendientes' ? 'selected' : '' }}>Pendientes</option>
                    <option value="entregados" {{ request('filtro') === 'entregados' ? 'selected' : '' }}>Entregados</option>
                </select>
                
                <!-- Mantener búsqueda en filtro -->
                @if(request('buscar'))
                <input type="hidden" name="buscar" value="{{ request('buscar') }}">
                @endif
            </form>
        </div>
    </div>

    <!-- Mensajes de éxito -->
    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Tabla de pedidos -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-black text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Nº de pedido</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Fecha de compra</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Cliente</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Detalle</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold">Fecha de entrega</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold">Importe</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold">Entregado</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pedidos as $pedido)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900">{{ $pedido->numero_pedido }}</span>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600">{{ $pedido->fecha_compra->format('d/m/Y') }}</span>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900">{{ $pedido->cliente->nombre }}</div>
                            <div class="text-gray-500">{{ $pedido->cliente->email }}</div>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <a 
                            href="{{ route('admin.pedidos.show', $pedido->id) }}"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium underline"
                        >
                            Ver detalle de compra
                        </a>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <form action="{{ route('admin.pedidos.updateFechaEntrega', $pedido->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input 
                                type="date" 
                                name="fecha_entrega"
                                value="{{ $pedido->fecha_entrega->format('Y-m-d') }}"
                                onchange="this.form.submit()"
                                class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500"
                            >
                        </form>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <span class="text-sm font-semibold text-gray-900">${{ number_format($pedido->total, 2, ',', '.') }}</span>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <form action="{{ route('admin.pedidos.toggleEntregado', $pedido->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <label class="flex items-center justify-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    {{ $pedido->entregado ? 'checked' : '' }}
                                    onchange="this.form.submit()"
                                    class="w-6 h-6 text-green-600 border-gray-300 rounded focus:ring-green-500 cursor-pointer"
                                >
                            </label>
                        </form>
                        @if($pedido->entregado && $pedido->fecha_entregado)
                        <div class="text-xs text-gray-500 mt-1">{{ $pedido->fecha_entregado->format('d/m/Y') }}</div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        No se encontraron pedidos
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-6">
        {{ $pedidos->appends(request()->query())->links() }}
    </div>
</div>
@endsection