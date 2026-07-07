<?php

namespace App\Livewire\Vistas\Productos;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Familia;
use App\Models\CategoriaTodotex;
use App\Models\ProductoTodotex;
use App\Models\Rubro;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.public2')]
class ProductosTodotexPage extends Component
{
    use WithPagination;

    #[Url(keep: true)]
    public $familiaId = null;

    #[Url(keep: true)]
    public $categoriaId = null;

    #[Url(keep: true)]
    public $rubroId = null;

    #[Url(keep: true)]
    public $search = '';

    #[Url(keep: true)]
    public $productoId = null;

    public function updatedSearch()
    {
        $this->productoId = null;
        $this->resetPage();
    }

    public function seleccionarFamilia($id)
    {
        $this->familiaId = $id;
        $this->rubroId = null;
        $this->categoriaId = null;
        $this->productoId = null;
        $this->resetPage();
    }

    public function seleccionarRubro($id)
    {
        $this->rubroId = $id;
        $this->familiaId = null;
        $this->categoriaId = null;
        $this->productoId = null;
        $this->resetPage();
    }

    public function seleccionarCategoria($id)
    {
        $this->categoriaId = $id;
        $this->familiaId = null;
        $this->rubroId = null;
        $this->productoId = null;
        $this->resetPage();
    }

    public function seleccionarTodos()
    {
        $this->familiaId = null;
        $this->categoriaId = null;
        $this->rubroId = null;
        $this->search = '';
        $this->productoId = null;
        $this->resetPage();
    }

    public function verProducto($id)
    {
        $this->productoId = $id;
    }

    public function volverAlListado()
    {
        $this->productoId = null;
    }

    public function render()
    {
        $familias = Familia::where('visible', true)
            ->with(['categorias' => fn($q) => $q->where('visible', true)->orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')])
            ->orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')
            ->get();

        $rubros = Rubro::with([
            'categorias' => fn($q) => $q->where('visible', true)->orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)'),
        ])
            ->orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')
            ->orderBy('titulo')
            ->get();

        if ($this->categoriaId) {
            $bannerImagen = CategoriaTodotex::find($this->categoriaId)?->imagen;
        } elseif ($this->familiaId) {
            $bannerImagen = $familias->firstWhere('id', $this->familiaId)?->imagen;
        } elseif ($this->rubroId) {
            $bannerImagen = $rubros->firstWhere('id', $this->rubroId)?->imagen;
        } else {
            $bannerImagen = DB::table('configuraciones')->where('clave', 'banner_todos_productos')->value('valor');
        }

        $producto = null;
        $productos = collect();
        $relacionados = collect();

        if ($this->productoId) {
            $producto = ProductoTodotex::with(['gallery', 'categorias.familia'])
                ->find($this->productoId);

            if ($producto) {
                $familiaId = $producto->categorias->first()?->familia_id;
                if ($familiaId) {
                    $relacionados = ProductoTodotex::where('visible', true)
                        ->where('id', '!=', $producto->id)
                        ->whereHas('categorias', fn($q) => $q->where('familia_id', $familiaId))
                        ->with(['gallery', 'categorias.familia'])
                        ->inRandomOrder()
                        ->limit(3)
                        ->get();
                }
            }
        } else {
            $query = ProductoTodotex::where('visible', true)
                ->with(['gallery', 'categorias.familia', 'categorias.rubro']);

            $filtroActivo = $this->categoriaId || $this->familiaId || $this->rubroId;

            if ($this->rubroId) {
                // Orden específico del rubro
                $rubroId = $this->rubroId;
                $query->leftJoin('rubro_producto_orden as rpo', function ($join) use ($rubroId) {
                    $join->on('rpo.producto_id', '=', 'productos_todotex.id')
                         ->where('rpo.rubro_id', '=', $rubroId);
                })
                ->select('productos_todotex.*')
                ->orderByRaw("rpo.orden IS NULL, CAST(rpo.orden AS UNSIGNED), CAST(REPLACE(productos_todotex.codigo, ' ', '') AS UNSIGNED)");
            } elseif ($this->categoriaId) {
                // Orden específico de la categoría (columna orden en pivot categoria_producto)
                $categoriaId = $this->categoriaId;
                $query->join('categoria_producto as cp_ord', function ($join) use ($categoriaId) {
                    $join->on('cp_ord.producto_id', '=', 'productos_todotex.id')
                         ->where('cp_ord.categoria_id', '=', $categoriaId);
                })
                ->select('productos_todotex.*')
                ->orderByRaw("cp_ord.orden IS NULL, CAST(cp_ord.orden AS UNSIGNED), CAST(REPLACE(productos_todotex.codigo, ' ', '') AS UNSIGNED)");
            } elseif ($filtroActivo) {
                // Con filtro de familia: orden por código
                $query->orderByRaw("CAST(REPLACE(codigo, ' ', '') AS UNSIGNED)");
            } else {
                // Sin filtro: productos con orden primero, luego los sin orden
                // agrupados por orden de familia
                $familiaOrdenSub = DB::table('categoria_producto as cp')
                    ->join('categorias_todotex as ct', 'ct.id', '=', 'cp.categoria_id')
                    ->join('familias as f', 'f.id', '=', 'ct.familia_id')
                    ->select('cp.producto_id', DB::raw('MIN(f.orden) as familia_orden'))
                    ->groupBy('cp.producto_id');

                $query->leftJoinSub($familiaOrdenSub, 'pf', 'pf.producto_id', '=', 'productos_todotex.id')
                    ->select('productos_todotex.*')
                    ->orderByRaw('(productos_todotex.orden IS NULL), CAST(productos_todotex.orden AS UNSIGNED), (pf.familia_orden IS NULL), CAST(pf.familia_orden AS UNSIGNED)');
            }

            if ($this->categoriaId) {
                $query->whereHas('categorias', fn($q) => $q->where('categorias_todotex.id', $this->categoriaId)->where('visible', true));
            } elseif ($this->familiaId) {
                $query->whereHas('categorias', fn($q) => $q->where('familia_id', $this->familiaId)->where('visible', true));
            } elseif ($this->rubroId) {
                $query->whereHas('categorias', fn($q) => $q->where('visible', true)->whereHas('rubros', fn($q2) => $q2->where('id', $this->rubroId)));
            }

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('titulo', 'like', "%{$this->search}%")
                      ->orWhere('codigo', 'like', "%{$this->search}%")
                      ->orWhere('descripcion', 'like', "%{$this->search}%");
                });
            }

            $productos = $query->paginate(24);
        }

        return view('livewire.vistas.productos.productos-todotex-page', [
            'familias'      => $familias,
            'rubros'        => $rubros,
            'productos'     => $productos,
            'producto'      => $producto,
            'relacionados'  => $relacionados,
            'bannerImagen'  => $bannerImagen,
        ]);
    }
}
