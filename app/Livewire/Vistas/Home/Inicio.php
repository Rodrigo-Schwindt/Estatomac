<?php

namespace App\Livewire\Vistas\Home;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Sliders;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;

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

    public function buscar()
    {
        return redirect()->route('productos')->with([
            'search' => $this->search,
            'tipo' => $this->tipo,
            'categoriaId' => $this->categoriaId,
            'marcaId' => $this->marcaId,
            'modeloId' => $this->modeloId,
            'codigo' => $this->codigo,
            'equivalencia' => $this->equivalencia,
            'filtrosSubcategorias' => $this->filtrosSubcategorias,
            'aplicarFiltros' => true,
        ]);
    }

    public function limpiarFiltros()
    {
        $this->reset([
            'search',
            'tipo',
            'categoriaId',
            'marcaId',
            'modeloId',
            'codigo',
            'equivalencia',
            'filtrosSubcategorias',
        ]);
    }

    public function seleccionarCategoria($categoriaId)
    {
        return redirect()->route('productos')->with([
            'categoriaId' => $categoriaId,
            'aplicarFiltros' => true,
        ]);
    }

    public function render()
    {
        $categorias = Categoria::where('destacado', true)
            ->with('subcategorias')
            ->orderBy('order')
            ->get();

        $marcas = Marca::with('modelos')
            ->orderBy('order')
            ->get();

        $subcategoriasActuales = [];
        if ($this->categoriaId) {
            $subcategoriasActuales = Categoria::find($this->categoriaId)
                ?->subcategorias()
                ->orderBy('order')
                ->get() ?? collect();
        }

        $productos = Producto::all();

        $equivalenciasDisponibles = \App\Models\Equivalencia::select('code', 'title')
            ->whereNotNull('code')
            ->distinct()
            ->orderBy('code')
            ->get();

        return view('livewire.vistas.home.inicio', [
            'categorias' => $categorias,
            'marcas' => $marcas,
            'productos' => $productos,
            'subcategoriasActuales' => $subcategoriasActuales,
            'equivalenciasDisponibles' => $equivalenciasDisponibles,
        ]);
    }
}