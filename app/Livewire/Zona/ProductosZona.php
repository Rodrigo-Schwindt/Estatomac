<?php

namespace App\Livewire\Zona;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Carrito;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.zone')]
class ProductosZona extends Component
{
    use WithPagination;

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
    
    public array $cantidades = [];
    public $filtrosSubcategorias = [];
    
    #[Url(keep: true)]
    public $aplicarFiltros = false;
    
    #[Url(keep: true)]
    public $perPage = 24;
    
    public $mostrarModalOfertas = false;
    public $productosEnOferta = [];
    public $productoActualModal = 0;

    public function mount()
    {
        if (!Auth::guard('cliente')->check()) {
            session()->flash('openLoginModal', true);
            return redirect()->route('home');
        }

        if (session()->has('toast')) {
            $toast = session('toast');
            $this->dispatch('producto-agregado', [
                'message' => $toast['message'],
                'type' => $toast['type']
            ]);
        }
        
        $this->verificarModalOfertas();

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

    public function verificarModalOfertas()
    {
        $clienteId = Auth::guard('cliente')->id();
        $cacheKey = 'modal_ofertas_' . $clienteId;
        $ultimaVista = session($cacheKey);
        
        if (!$ultimaVista || (time() - $ultimaVista) > 140) {
            $this->productosEnOferta = Producto::where('visible', true)
                ->where('oferta', true)
                ->with(['gallery'])
                ->get()
                ->toArray();
            
            if (count($this->productosEnOferta) > 0) {
                $this->dispatch('mostrar-modal-ofertas-delayed');
                session([$cacheKey => time()]);
            }
        }
    }

    public function cerrarModalOfertas()
    {
        $this->mostrarModalOfertas = false;
    }

    public function siguienteProductoModal()
    {
        if ($this->productoActualModal < count($this->productosEnOferta) - 1) {
            $this->productoActualModal++;
        } else {
            $this->productoActualModal = 0;
        }
    }

    public function anteriorProductoModal()
    {
        if ($this->productoActualModal > 0) {
            $this->productoActualModal--;
        }
    }

    public function agregarAlCarritoDesdeModal($productoId)
    {
        $this->agregarAlCarrito($productoId);
    }

    public function agregarAlCarrito($productoId)
    {
        $producto = Producto::find($productoId);
        
        if (!$producto) {
            $this->dispatch('show-toast', message: 'Producto no encontrado', type: 'error');
            return;
        }

        $cantidad = $this->cantidades[$productoId] ?? 1;
        $clienteId = Auth::guard('cliente')->id();

        $itemCarrito = Carrito::where('cliente_id', $clienteId)
            ->where('producto_id', $productoId)
            ->first();

        if ($itemCarrito) {
            $itemCarrito->cantidad += $cantidad;
            $itemCarrito->save();
            $mensaje = "¡{$producto->code} actualizado en el carrito!";
        } else {
            Carrito::create([
                'cliente_id' => $clienteId,
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
                'precio_unitario' => $producto->precio,
                'descuento_unitario' => $producto->descuento ?? 0,
            ]);
            $mensaje = "¡{$producto->code} agregado al carrito!";
        }

        $this->dispatch('producto-agregado', ['message' => $mensaje]);
        $this->dispatch('carrito-actualizado');
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

    public function incrementar($productoId)
    {
        if (!isset($this->cantidades[$productoId])) {
            $this->cantidades[$productoId] = 1;
        }

        $this->cantidades[$productoId]++;
    }

    public function decrementar($productoId)
    {
        if (
            isset($this->cantidades[$productoId]) &&
            $this->cantidades[$productoId] > 1
        ) {
            $this->cantidades[$productoId]--;
        }
    }

    public function render()
    {
        $categorias = Categoria::with('subcategorias')
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

        $hayFiltros = $this->hayFiltrosActivos();

        $productos = $this->filtrarProductos();

        if ($productos) {
            foreach ($productos as $producto) {
                if (!isset($this->cantidades[$producto->id])) {
                    $this->cantidades[$producto->id] = 1;
                }
            }
        }

        $equivalenciasDisponibles = \App\Models\Equivalencia::select('code', 'title')
            ->whereNotNull('code')
            ->distinct()
            ->orderBy('code')
            ->get();

        return view('livewire.zona.productos-zona', [
            'categorias' => $categorias,
            'marcas' => $marcas,
            'productos' => $productos,
            'hayFiltros' => $hayFiltros,
            'subcategoriasActuales' => $subcategoriasActuales,
            'equivalenciasDisponibles' => $equivalenciasDisponibles,
        ]);
    }

    protected function filtrarProductos()
    {
        $query = Producto::query()
            ->with([
                'marcas',
                'modelos',
                'categoria',
                'subcategorias',
                'equivalencias',
                'gallery',
            ])
            ->where('visible', true);

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
}