<?php

namespace App\Observers;

use App\Models\CategoriaTodotex;
use Illuminate\Support\Facades\DB;

class ProductoTodotexObserver
{
    /**
     * Dispara cuando sync() quita categorías de un producto.
     * En ese momento el pivot ya está actualizado → chequeo correcto.
     */
    public function pivotDetached(): void
    {
        $this->ocultarCategoriasVacias();
    }

    public function deleted(): void
    {
        $this->ocultarCategoriasVacias();
    }

    private function ocultarCategoriasVacias(): void
    {
        $conProductos = DB::table('categoria_producto')
            ->distinct()
            ->pluck('categoria_id');

        CategoriaTodotex::where('visible', true)
            ->whereNotIn('id', $conProductos)
            ->update(['visible' => false]);
    }
}
