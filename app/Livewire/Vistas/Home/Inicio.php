<?php

namespace App\Livewire\Vistas\Home;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Sliders;


#[Layout('layouts.public2')]
class Inicio extends Component
{
    public $sliders;
    public $search = '';
    public $tipo = null;
    public $categoriaId = null;
    public $marcaId = null;
    public $modeloId = null;
    public $codigo = null;
    public $equivalencia = null;
    public $filtrosSubcategorias = [];

    public function mount()
    {
        $this->sliders = Sliders::orderBy('orden')->get();
    }

    public function isVideo($filename)
    {
        $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return in_array($extension, $videoExtensions);
    }

    public function getMimeType($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $mimeTypes = [
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
        ];

        return $mimeTypes[$extension] ?? 'video/mp4';
    }

    public function updatedCategoriaId()
    {
        $this->filtrosSubcategorias = [];
    }

    public function updatedMarcaId()
    {
        $this->modeloId = null;
    }

    public function toggleTipo($valor)
    {
        if ($this->tipo === $valor) {
            $this->tipo = null;
        } else {
            $this->tipo = $valor;
        }
    }



  
    public function render()
    {
  
        return view('livewire.vistas.home.inicio', [

        ]);
    }
}