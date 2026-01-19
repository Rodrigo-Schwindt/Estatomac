<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with(['cliente', 'items']);

        // Búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('numero_pedido', 'like', '%' . $buscar . '%')
                  ->orWhereHas('cliente', function ($query) use ($buscar) {
                      $query->where('nombre', 'like', '%' . $buscar . '%')
                            ->orWhere('email', 'like', '%' . $buscar . '%');
                  });
            });
        }

        // Filtro por estado
        if ($request->filled('filtro') && $request->filtro !== 'todos') {
            $entregado = $request->filtro === 'entregados';
            $query->where('entregado', $entregado);
        }

        $pedidos = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('livewire.pedidos.index', compact('pedidos'));
    }

    public function show($id)
    {
        $pedido = Pedido::with(['cliente', 'items.producto'])->findOrFail($id);
        return view('livewire.pedidos.show', compact('pedido'));
    }

    public function updateFechaEntrega(Request $request, $id)
    {
        $request->validate([
            'fecha_entrega' => 'required|date',
        ]);

        $pedido = Pedido::findOrFail($id);
        $pedido->update([
            'fecha_entrega' => $request->fecha_entrega,
        ]);

        return back()->with('success', 'Fecha de entrega actualizada correctamente');
    }

    public function toggleEntregado($id)
    {
        $pedido = Pedido::findOrFail($id);

        if ($pedido->entregado) {
            $pedido->update([
                'entregado' => false,
                'fecha_entregado' => null,
            ]);
            $mensaje = 'Pedido marcado como pendiente';
        } else {
            $pedido->marcarComoEntregado();
            $mensaje = 'Pedido marcado como entregado';
        }

        return back()->with('success', $mensaje);
    }
}