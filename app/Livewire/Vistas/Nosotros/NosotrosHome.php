<?php

namespace App\Livewire\Vistas\Nosotros;

use Livewire\Component;
use App\Models\Nosotros;

class NosotrosHome extends Component
{
    public function render()
    {
        $nosotros = Nosotros::first();
        
        return view('livewire.vistas.home.nosotros-home', compact('nosotros'));
    }
}