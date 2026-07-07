<?php

namespace App\Livewire\Vistas\Productos;

use Livewire\Component;
use App\Models\ProductoTodotex;

class ProductosDestacados extends Component
{
    public function render()
    {
        $destacados = ProductoTodotex::where('visible', true)
            ->where('destacado', true)
            ->with(['gallery', 'categorias.familia'])
            ->orderBy('orden')
            ->take(12)
            ->get();

        return view('livewire.vistas.productos.productos-destacados', [
            'destacados' => $destacados
        ]);
    }
}