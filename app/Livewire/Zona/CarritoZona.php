<?php

namespace App\Livewire\Zona;

use Livewire\Component;
use App\Models\Carrito;
use App\Models\CarritoConfig;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.zone')]
class CarritoZona extends Component
{
    public $formaPago = 'contado';

    protected $listeners = ['carrito-actualizado' => '$refresh'];

    public function mount()
    {
        if (!Auth::guard('cliente')->check()) {
            session()->flash('openLoginModal', true);
            return redirect()->route('home');
        }
    }

    #[On('carrito-actualizado')]
    public function actualizarCarrito()
    {
    }

    public function incrementar($carritoId)
    {
        $item = Carrito::find($carritoId);
        if ($item && $item->cliente_id === Auth::guard('cliente')->id()) {
            $item->cantidad++;
            $item->save();
        }
    }

    public function decrementar($carritoId)
    {
        $item = Carrito::find($carritoId);
        if ($item && $item->cliente_id === Auth::guard('cliente')->id() && $item->cantidad > 1) {
            $item->cantidad--;
            $item->save();
        }
    }

    public function eliminar($carritoId)
    {
        $item = Carrito::find($carritoId);
        if ($item && $item->cliente_id === Auth::guard('cliente')->id()) {
            $codigo = $item->producto->code ?? 'Producto';
            $item->delete();
            
            // 🎉 Usar el mismo evento que en productos
            $this->dispatch('producto-agregado', [
                'message' => "¡{$codigo} eliminado del carrito!"
            ]);
        }
    }

    public function render()
    {
        $config = CarritoConfig::first();
        
        $items = Carrito::with('producto')
            ->where('cliente_id', Auth::guard('cliente')->id())
            ->get();

        $subtotalSinDescuento = $items->sum(function ($item) {
            return $item->precio_unitario * $item->cantidad;
        });

        $descuentos = $items->sum(function ($item) {
            return $item->descuento_unitario * $item->cantidad;
        });

        $subtotal = $subtotalSinDescuento - $descuentos;
        
        $descuentoPorPago = 0;
        if ($config) {
            if ($this->formaPago === 'contado') {
                $descuentoPorPago = $subtotal * ($config->contado / 100);
            } elseif ($this->formaPago === 'transferencia') {
                $descuentoPorPago = $subtotal * ($config->transferencia / 100);
            } elseif ($this->formaPago === 'cuenta_corriente') {
                $descuentoPorPago = $subtotal * ($config->corriente / 100);
            }
        }

        $subtotalConDescuentoPago = $subtotal - $descuentoPorPago;
        
        $porcentajeIva = $config ? $config->iva / 100 : 0.21;
        $iva = $subtotalConDescuentoPago * $porcentajeIva;
        
        $totalFinal = $subtotalConDescuentoPago + $iva;

        return view('livewire.zona.carrito-zona', [
            'config' => $config,
            'items' => $items,
            'subtotalSinDescuento' => $subtotalSinDescuento,
            'descuentos' => $descuentos,
            'subtotal' => $subtotal,
            'descuentoPorPago' => $descuentoPorPago,
            'subtotalConDescuentoPago' => $subtotalConDescuentoPago,
            'iva' => $iva,
            'total' => $totalFinal,
        ]);
    }
}