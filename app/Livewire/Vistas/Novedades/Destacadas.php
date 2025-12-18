<?php

namespace App\Livewire\Vistas\Novedades;
use Livewire\Component;
use App\Models\Novedades;

class Destacadas extends Component
{
    public function render()
    {
        $destacadas = Novedades::where('destacado', true)
            ->orderBy('orden', 'asc')
            ->take(9)
            ->get();

        return view('livewire.novedades.destacadas', compact('destacadas'));
    }
}