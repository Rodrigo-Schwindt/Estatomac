<?php

namespace App\Livewire\Zona;

use Livewire\Component;
use App\Models\Carrito;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.zone')]
class MisPedidos extends Component
{
    public bool  $esVendedor      = false;
    public bool  $carritoTieneItems = false;
    public array $seleccionados   = [];
    public string $ordenar        = 'fecha'; // 'fecha' | 'cliente'

    public function mount(): void
    {
        if (Auth::guard('vendedor')->check()) {
            $this->esVendedor = true;
            $clienteId = session('vendedor_cliente_id');
            if ($clienteId) {
                $this->carritoTieneItems = Carrito::where('cliente_id', $clienteId)->exists();
            }
            return;
        }

        if (!Auth::guard('cliente')->check()) {
            session()->flash('openLoginModal', true);
            $this->redirect(route('home'));
            return;
        }

        $this->carritoTieneItems = Carrito::where('cliente_id', Auth::guard('cliente')->id())->exists();
    }

    public function anularSeleccionados(): void
    {
        if (empty($this->seleccionados)) {
            return;
        }

        if ($this->carritoTieneItems) {
            $this->dispatch('producto-agregado', ['message' => 'No podés anular pedidos mientras hay ítems en el carrito.', 'type' => 'error']);
            return;
        }

        $query = Pedido::whereIn('id', $this->seleccionados)
            ->where('anulado', false)
            ->whereNull('enviado_erp_at');

        if ($this->esVendedor) {
            $query->where('vendedor_id', Auth::guard('vendedor')->id());
        } else {
            $query->where('cliente_id', Auth::guard('cliente')->id());
        }

        $count = $query->count();
        $query->update(['anulado' => true, 'fecha_anulado' => now()]);

        $this->seleccionados = [];
        $this->dispatch('producto-agregado', [
            'message' => "{$count} " . ($count === 1 ? 'pedido anulado.' : 'pedidos anulados.'),
            'type'    => 'success',
        ]);
    }

    public function render()
    {
        $query = $this->esVendedor
            ? Pedido::with(['items', 'cliente'])->where('vendedor_id', Auth::guard('vendedor')->id())
            : Pedido::with(['items', 'vendedor'])->where('cliente_id', Auth::guard('cliente')->id());

        if ($this->esVendedor && $this->ordenar === 'cliente') {
            $query->join('clientes', 'pedidos.cliente_id', '=', 'clientes.id')
                  ->orderBy('clientes.nombre')
                  ->select('pedidos.*');
        } else {
            $query->orderBy('pedidos.created_at', 'desc');
        }

        $pedidos = $query->get();

        $hayPendientes = $pedidos->contains(fn ($p) => !$p->anulado && $p->enviado_erp_at === null);

        return view('livewire.zona.mis-pedidos', [
            'pedidos'           => $pedidos,
            'esVendedor'        => $this->esVendedor,
            'hayPendientes'     => $hayPendientes,
            'carritoTieneItems' => $this->carritoTieneItems,
        ]);
    }
}
