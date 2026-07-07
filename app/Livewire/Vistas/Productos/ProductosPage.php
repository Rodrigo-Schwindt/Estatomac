<?php

namespace App\Livewire\Vistas\Productos;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Banner;
use App\Models\CategoriaPadre;

#[Layout('layouts.public')]
class ProductosPage extends Component
{
    use WithPagination;

    public $categoriaPadreSlug = null;
    public $categoriaPadreId = null;

    #[Url(keep: true)]
    public $search = '';
    
    #[Url(keep: true)]
    public $tipo = null;
    
    #[Url(keep: true)]
    public $categoriaId = null;
    
    #[Url(keep: true)]
    public $marcaId = null;
    
    #[Url(keep: true)]
    public $modeloId = null;
    
    #[Url(keep: true)]
    public $codigo = null;
    
    #[Url(keep: true)]
    public $equivalencia = null;
    
    public $filtrosSubcategorias = [];
    
    #[Url(keep: true)]
    public $aplicarFiltros = false;

    #[Url(keep: true)]
    public $perPage = 24;

    public function mount($categoriaPadre = null)
    {
        if ($categoriaPadre) {
            $categoriaPadreModel = CategoriaPadre::where('title', 'like', '%' . str_replace('-', ' ', $categoriaPadre) . '%')->first();
            
            if ($categoriaPadreModel) {
                $this->categoriaPadreId = $categoriaPadreModel->id;
                $this->categoriaPadreSlug = $categoriaPadre;
            }
        }

        if (session()->has('search')) {
            $this->search = session('search');
        }
        if (session()->has('tipo')) {
            $this->tipo = session('tipo');
        }
        if (session()->has('categoriaId')) {
            $this->categoriaId = session('categoriaId');
        }
        if (session()->has('marcaId')) {
            $this->marcaId = session('marcaId');
        }
        if (session()->has('modeloId')) {
            $this->modeloId = session('modeloId');
        }
        if (session()->has('codigo')) {
            $this->codigo = session('codigo');
        }
        if (session()->has('equivalencia')) {
            $this->equivalencia = session('equivalencia');
        }
        if (session()->has('filtrosSubcategorias')) {
            $this->filtrosSubcategorias = session('filtrosSubcategorias');
        }
        if (session()->has('aplicarFiltros')) {
            $this->aplicarFiltros = session('aplicarFiltros');
        }
    }

    public function updatedCategoriaId()
    {
        $this->filtrosSubcategorias = [];
        $this->resetPage();
    }

    public function updatedMarcaId()
    {
        $this->modeloId = null;
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function buscar()
    {
        $this->aplicarFiltros = true;
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->reset([
            'search',
            'tipo',
            'categoriaId',
            'filtrosSubcategorias',
            'marcaId',
            'modeloId',
            'codigo',
            'equivalencia',
            'aplicarFiltros',
        ]);
        $this->categoriaPadreId = null;
        $this->categoriaPadreSlug = null;
        $this->resetPage();
    }

    public function hayFiltrosActivos(): bool
    {
        return $this->aplicarFiltros && (
            filled($this->search)
            || filled($this->tipo)
            || filled($this->categoriaId)
            || !empty(array_filter($this->filtrosSubcategorias))
            || filled($this->marcaId)
            || filled($this->modeloId)
            || filled($this->codigo)
            || filled($this->equivalencia)
            || filled($this->categoriaPadreId)
        );
    }

    public function seleccionarCategoria($categoriaId)
    {
        $this->categoriaId = $categoriaId;
        $this->buscar();
    }

    public function toggleTipo($valor)
    {
        if ($this->tipo === $valor) {
            $this->tipo = null;
        } else {
            $this->tipo = $valor;
        }
        $this->resetPage();
    }

    protected function filtrarProductos()
    {
        $query = Producto::query()
            ->with([
                'marcas',
                'modelos',
                'categoria',
                'categoria.categoriasPadre',
                'subcategorias',
                'equivalencias',
                'gallery',
            ])
            ->where('visible', true);

        if ($this->categoriaPadreId) {
            $query->whereHas('categoria.categoriasPadre', function($q) {
                $q->where('categoria_padres.id', $this->categoriaPadreId);
            });
        }

        if ($this->tipo) {
            $query->where('nuevo', $this->tipo);
        }

        if ($this->categoriaId) {
            $query->where('categoria_id', $this->categoriaId);
        }

        if (!empty(array_filter($this->filtrosSubcategorias))) {
            foreach ($this->filtrosSubcategorias as $subcategoriaId => $valor) {
                if (!empty($valor)) {
                    $query->whereHas('subcategorias', function ($q) use ($subcategoriaId, $valor) {
                        $q->where('subcategorias.id', $subcategoriaId)
                          ->where('producto_subcategoria.valor', 'like', "%{$valor}%");
                    });
                }
            }
        }

        if ($this->marcaId) {
            $query->whereHas('marcas', function($q) {
                $q->where('marcas.id', $this->marcaId);
            });
        }

        if ($this->modeloId) {
            $query->whereHas('modelos', function($q) {
                $q->where('modelos.id', $this->modeloId);
            });
        }

        if ($this->codigo) {
            $codigoNormalizado = str_replace(['-', '/'], '', $this->codigo);
            $query->whereRaw("REPLACE(REPLACE(code, '-', ''), '/', '') LIKE ?", ["%{$codigoNormalizado}%"]);
        }

        if ($this->equivalencia) {
            $equivalenciaNormalizada = str_replace(['-', '/'], '', $this->equivalencia);
            $query->whereHas('equivalencias', function ($q) use ($equivalenciaNormalizada) {
                $q->whereRaw("REPLACE(REPLACE(code, '-', ''), '/', '') LIKE ?", ["%{$equivalenciaNormalizada}%"])
                  ->orWhereRaw("REPLACE(REPLACE(title, '-', ''), '/', '') LIKE ?", ["%{$equivalenciaNormalizada}%"]);
            });
        }

        if ($this->search) {
            $searchNormalizado = str_replace(['-', '/'], '', $this->search);
            $query->where(function ($q) use ($searchNormalizado) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhereRaw("REPLACE(REPLACE(code, '-', ''), '/', '') LIKE ?", ["%{$searchNormalizado}%"])
                  ->orWhereHas('categoria', fn($c) => $c->where('title', 'like', "%{$this->search}%"))
                  ->orWhereHas('marcas', fn($m) => $m->where('title', 'like', "%{$this->search}%"))
                  ->orWhereHas('modelos', fn($m) => $m->where('title', 'like', "%{$this->search}%"))
                  ->orWhereHas('subcategorias', fn($s) => $s->where('title', 'like', "%{$this->search}%"))
                  ->orWhereHas('equivalencias', function($e) use ($searchNormalizado) {
                      $e->whereRaw("REPLACE(REPLACE(code, '-', ''), '/', '') LIKE ?", ["%{$searchNormalizado}%"])
                        ->orWhereRaw("REPLACE(REPLACE(title, '-', ''), '/', '') LIKE ?", ["%{$searchNormalizado}%"]);
                  });
            });
        }

        return $query->orderBy('order')->paginate($this->perPage);
    }

    public function render()
    {
        $categoriasQuery = Categoria::with('subcategorias');
        
        if ($this->categoriaPadreId) {
            $categoriasQuery->whereHas('categoriasPadre', function($q) {
                $q->where('categoria_padres.id', $this->categoriaPadreId);
            });
        }
        
        $categorias = $categoriasQuery->orderBy('order')->get();
    
        $marcas = Marca::with('modelos')
            ->orderBy('order')
            ->get();
    
        $banner = Banner::forSection('categorias')->first();
    
        $subcategoriasActuales = [];
        if ($this->categoriaId) {
            $subcategoriasActuales = Categoria::find($this->categoriaId)
                ?->subcategorias()
                ->orderBy('order')
                ->get() ?? collect();
        }
    
        $hayFiltros = $this->hayFiltrosActivos();
        $productos = $hayFiltros ? $this->filtrarProductos() : null;
        
        $equivalenciasDisponibles = \App\Models\Equivalencia::select('code', 'title')
            ->whereNotNull('code')
            ->distinct()
            ->orderBy('code')
            ->get();
    
        return view('livewire.vistas.productos.productos-page', [
            'categorias' => $categorias,
            'marcas' => $marcas,
            'productos' => $productos,
            'hayFiltros' => $hayFiltros,
            'banner' => $banner,
            'subcategoriasActuales' => $subcategoriasActuales,
            'equivalenciasDisponibles' => $equivalenciasDisponibles,
        ]);
    }
}