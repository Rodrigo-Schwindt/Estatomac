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

    public function updateFechaEntrega(Request $request, Pedido $pedido)
    {
        $nuevaFecha = $request->input('fecha_entrega');
    
        if ($nuevaFecha) {
            $pedido->update([
                'fecha_entrega' => $nuevaFecha,
                'entregado' => true,
                'fecha_entregado' => now(), 
            ]);
        } else {
            $pedido->update([
                'fecha_entrega' => null,
                'entregado' => false,
                'fecha_entregado' => null,
            ]);
        }
    
        return back()->with('success', 'Pedido actualizado correctamente');
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