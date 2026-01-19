<?php

namespace App\Livewire\Zona;

use Livewire\Component;
use App\Models\Precio;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;

#[Layout('layouts.zone')]
class Precios extends Component
{
    public function descargar($id)
    {
        $precio = Precio::findOrFail($id);
    
        return Storage::disk('public')->download($precio->archivo);
    }
    

    public function render()
    {
        return view('livewire.zona.precios', [
            'precios' => Precio::orderBy('created_at', 'desc')->get(),
        ]);
    }
}
