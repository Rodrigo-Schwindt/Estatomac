<?php

namespace App\Livewire\Vistas\Productos;

use Livewire\Component;
use App\Models\Producto;

class ProductosDestacados extends Component
{
    public function render()
    {
        $destacados = Producto::where('visible', true)
            ->where('destacado', true)
            ->orderBy('order')
            ->take(12)
            ->get();

        return view('livewire.vistas.productos.productos-destacados', [
            'destacados' => $destacados
        ]);
    }
}