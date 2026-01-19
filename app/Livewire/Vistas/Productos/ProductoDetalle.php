<?php

namespace App\Livewire\Vistas\Productos;

use Livewire\Component;
use App\Models\Producto;
use Livewire\Attributes\Layout;


#[Layout('layouts.public2')]
class ProductoDetalle extends Component
{
    public Producto $producto;
    public $relacionados;
    public $imagenActual;

    public function mount($id)
    {   
        $this->producto = Producto::with([
                'marca',
                'modelo',
                'categoria',
                'subcategorias',
                'gallery'
            ])
            ->where('visible', true)
            ->findOrFail($id);

        $this->imagenActual = $this->producto->image;

        $this->relacionados = Producto::where('categoria_id', $this->producto->categoria_id)
            ->where('id', '!=', $this->producto->id)
            ->where('visible', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();
    }

    public function cambiarImagen($imagen)
    {
        $this->imagenActual = $imagen;
    }

    public function render()
    {
        return view('livewire.vistas.productos.producto-detalle');
    }
}