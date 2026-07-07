<?php

namespace App\Livewire\Zona;

use App\Models\Precio;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;

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
        $vigente = Precio::activa();

        return view('livewire.zona.precios', [
            'vigente' => $vigente,
        ]);
    }
}
