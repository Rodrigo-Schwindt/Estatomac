<?php

namespace App\Livewire\Zona;

use Livewire\Component;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.zone')]
class DetallePedido extends Component
{
    public $pedidoId;
    public $pedido;

    public function mount($id)
    {
        $this->pedidoId = $id;
        $this->pedido = Pedido::with(['cliente', 'items.producto'])
            ->where('id', $id)
            ->where('cliente_id', Auth::guard('cliente')->id())
            ->firstOrFail();
    }

    public function descargar()
    {
        if (!$this->pedido->descarga_habilitada) {
            $this->dispatch('show-toast', message: 'La descarga aún no está habilitada para este pedido', type: 'error');
            return;
        }

        return redirect()->route('cliente.pedidos.descargar', $this->pedido->id);
    }

    public function render()
    {
        return view('livewire.zona.detalle-pedido');
    }
}