<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Pedido;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class PedidosAdmin extends Component
{
    use WithPagination;

    public $buscar = '';
    public $filtroEntregado = 'todos';
    public $pedidoSeleccionadoId = null;
    public $modalDetalle = false;
    
    public function updatingBuscar()
    {
        $this->resetPage();
    }

    public function abrirModal($pedidoId)
    {
        $this->pedidoSeleccionadoId = $pedidoId;
        $this->modalDetalle = true;
    }

    public function cerrarModal()
    {
        $this->modalDetalle = false;
        $this->pedidoSeleccionadoId = null;
    }

    public function toggleEntregado($pedidoId)
    {
        $pedido = Pedido::find($pedidoId);
        
        if ($pedido) {
            if ($pedido->entregado) {
                $pedido->update([
                    'entregado' => false,
                    'fecha_entregado' => null,
                ]);
            } else {
                $pedido->marcarComoEntregado();
            }
            
            $this->dispatch('show-toast', message: 'Estado actualizado', type: 'success');
        }
    }

    public function actualizarFechaEntrega($pedidoId, $nuevaFecha)
    {
        $pedido = Pedido::find($pedidoId);
        
        if ($pedido) {
            $pedido->update(['fecha_entrega' => $nuevaFecha]);
            $this->dispatch('show-toast', message: 'Fecha de entrega actualizada', type: 'success');
        }
    }

    public function render()
    {
        $query = Pedido::with(['cliente', 'items'])
            ->when($this->buscar, function ($q) {
                $q->where(function ($query) {
                    $query->where('numero_pedido', 'like', '%' . $this->buscar . '%')
                        ->orWhereHas('cliente', function ($q) {
                            $q->where('nombre', 'like', '%' . $this->buscar . '%')
                              ->orWhere('email', 'like', '%' . $this->buscar . '%');
                        });
                });
            })
            ->when($this->filtroEntregado !== 'todos', function ($q) {
                $q->where('entregado', $this->filtroEntregado === 'entregados');
            })
            ->orderBy('created_at', 'desc');

        $pedidos = $query->paginate(20);
        
        $pedidoSeleccionado = null;
        if ($this->pedidoSeleccionadoId) {
            $pedidoSeleccionado = Pedido::with(['cliente', 'items.producto'])
                ->find($this->pedidoSeleccionadoId);
        }

        return view('livewire.admin.pedidos-admin', [
            'pedidos' => $pedidos,
            'pedidoSeleccionado' => $pedidoSeleccionado,
        ]);
    }
}