<?php

namespace App\Livewire\Zona;

use Livewire\Component;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.zone')]
class MisPedidos extends Component
{
    public function descargar($pedidoId)
    {
        $pedido = Pedido::where('id', $pedidoId)
            ->where('cliente_id', Auth::guard('cliente')->id())
            ->firstOrFail();

        // Disparar evento JavaScript para abrir la descarga
        $this->dispatch('open-download', url: route('cliente.pedidos.descargar', $pedido->id));
    }

    public function render()
    {
        $pedidos = Pedido::where('cliente_id', Auth::guard('cliente')->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.zona.mis-pedidos', [
            'pedidos' => $pedidos,
        ]);
    }
}