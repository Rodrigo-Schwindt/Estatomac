<?php

namespace App\Livewire\Zona;

use App\Models\Carrito;
use App\Models\CategoriaTodotex;
use App\Models\Cliente;
use App\Models\Familia;
use App\Models\ProductoTodotex;
use App\Models\Rubro;
use App\Models\Vendedor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.zone')]
class ProductosZona extends Component
{
    use WithPagination;

    #[Url(keep: true)]
    public $search = '';

    #[Url(keep: true)]
    public $familiaId = null;

    #[Url(keep: true)]
    public $categoriaId = null;

    #[Url(keep: true)]
    public $rubroId = null;

    #[Url(keep: true)]
    public $perPage = 24;

    public array  $cantidades       = [];
    public array  $cantidadesBultos = [];
    public array  $descuentosPersonalizados = [];

    // Estado del usuario
    public bool   $esVendedor             = false;
    public float  $descuentoBase          = 0;
    public ?int   $clienteSeleccionadoId  = null;
    public array  $clientesLista          = [];

    // OperaComo
    public ?int   $vendedorOperativoId     = null;
    public string $vendedorOperativoNombre = '';
    public bool   $operandoEnNombreDe      = false;
    public string $vendedorLogueadoNombre  = '';

    // Datos del cliente activo para el encabezado
    public ?array $clienteData = null;

    public bool   $carritoTieneItems = false;

    public function mount(): void
    {
        if (Auth::guard('vendedor')->check()) {
            $this->esVendedor = true;
            $vendedorLogueado = Auth::guard('vendedor')->user();
            $this->vendedorLogueadoNombre = $vendedorLogueado->nombre;

            // Resolver vendedor operativo
            $vendedorOperativo = $this->resolveVendedorOperativo($vendedorLogueado);
            if ($vendedorOperativo === null) {
                // Error de inconsistencia — redirigir
                session()->flash('error.zona', 'ERROR: Inconsistencia en vendedor operativo. Comuníquese con Soporte de Ventas.');
                $this->redirect(route('home'));
                return;
            }

            $this->vendedorOperativoId     = $vendedorOperativo->id;
            $this->vendedorOperativoNombre = $vendedorOperativo->nombre;
            $this->operandoEnNombreDe      = ($vendedorLogueado->id !== $vendedorOperativo->id);

            $this->clientesLista = Cliente::select('id', 'codigo', 'nombre', 'nombre_fantasia', 'descuento_canal', 'canal')
                ->where('activo', true)
                ->where('vendedor_id', $vendedorOperativo->id)
                ->orderBy('nombre')
                ->get()
                ->map(fn ($c) => [
                    'id'              => $c->id,
                    'codigo'          => (string) ($c->codigo ?? ''),
                    'nombre'          => $c->nombre,
                    'nombre_fantasia' => (string) ($c->nombre_fantasia ?? ''),
                    'descuento_canal' => (float) ($c->descuento_canal ?? 0),
                    'canal'           => (string) ($c->canal ?? ''),
                ])
                ->toArray();

            $clienteId = session('vendedor_cliente_id');
            if ($clienteId) {
                $cliente = Cliente::with('vendedor')->find($clienteId);
                if ($cliente && $cliente->vendedor_id === $vendedorOperativo->id) {
                    $this->clienteSeleccionadoId = $cliente->id;
                    $this->descuentoBase         = (float) ($cliente->descuento_canal ?? 0);
                    $this->clienteData           = $this->buildClienteData($cliente, $vendedorOperativo);
                    $this->carritoTieneItems     = Carrito::where('cliente_id', $cliente->id)->exists();
                }
            }

        } elseif (Auth::guard('cliente')->check()) {
            $this->esVendedor = false;
            $cliente = Auth::guard('cliente')->user()->load('vendedor');
            $this->descuentoBase         = (float) ($cliente->descuento_canal ?? 0);
            $this->clienteSeleccionadoId = $cliente->id;
            $this->clienteData           = $this->buildClienteData($cliente, $cliente->vendedor);
            $this->carritoTieneItems     = Carrito::where('cliente_id', $cliente->id)->exists();

        } else {
            session()->flash('openLoginModal', true);
            $this->redirect(route('home'));
            return;
        }

        if (session()->has('toast')) {
            $toast = session('toast');
            $this->dispatch('producto-agregado', [
                'message' => $toast['message'],
                'type'    => $toast['type'],
            ]);
        }
    }

    // ─── Resolución del vendedor operativo ──────────────────────────────────────
    private function resolveVendedorOperativo(Vendedor $vendedorLogueado): ?Vendedor
    {
        if (!$vendedorLogueado->opera_como) {
            return $vendedorLogueado;
        }

        $suplantado = Vendedor::find($vendedorLogueado->opera_como);

        if (!$suplantado || !$suplantado->activo) {
            return null; // inconsistencia
        }

        return $suplantado;
    }

    // ─── Construcción del array de datos del cliente para el header ─────────────
    private function buildClienteData(Cliente $cliente, ?Vendedor $vendedorOperativo): array
    {
        return [
            'codigo'           => $cliente->codigo,
            'nombre'           => $cliente->nombre,
            'nombre_fantasia'  => $cliente->nombre_fantasia,
            'domicilio'        => $cliente->domicilio,
            'localidad'        => $cliente->localidad,
            'provincia'        => $cliente->provincia,
            'codigo_postal'    => $cliente->codigo_postal,
            'cuit'             => $cliente->cuit,
            'condicion_iva'    => $cliente->condicion_iva,
            'condicion_venta'  => $cliente->condicion_venta,
            'transporte'       => $cliente->transporte,
            'canal'            => $cliente->canal,
            'descuento_canal'  => $cliente->descuento_canal,
            'vendedor_nombre'  => $vendedorOperativo?->nombre ?? $cliente->vendedor?->nombre,
        ];
    }

    // ─── Vendedor: selecciona cliente ────────────────────────────────────────────
    public function seleccionarCliente(int $clienteId): void
    {
        $cliente = Cliente::with('vendedor')->find($clienteId);
        if (!$cliente) return;

        if ($this->carritoTieneItems) {
            $this->dispatch('producto-agregado', ['message' => 'No podés cambiar de cliente mientras hay ítems en el carrito.', 'type' => 'error']);
            return;
        }

        // Solo permitir clientes del vendedor operativo
        if ($this->vendedorOperativoId && $cliente->vendedor_id !== $this->vendedorOperativoId) {
            $this->dispatch('producto-agregado', ['message' => 'Este cliente no pertenece al vendedor operativo.', 'type' => 'error']);
            return;
        }

        $this->clienteSeleccionadoId = $cliente->id;
        $this->descuentoBase         = (float) ($cliente->descuento_canal ?? 0);
        $this->descuentosPersonalizados = [];
        $this->clienteData           = $this->buildClienteData($cliente, Vendedor::find($this->vendedorOperativoId));
        session(['vendedor_cliente_id' => $cliente->id]);
    }

    // ─── Vendedor: agrega descuento personalizado en cascada ─────────────────
    public function setDescuentoPersonalizado(int $productoId, mixed $valor): void
    {
        $this->descuentosPersonalizados[$productoId] = max(0, min(100, (float) $valor));
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────
    private function resolveClienteId(): ?int
    {
        if ($this->esVendedor) {
            return $this->clienteSeleccionadoId;
        }

        return Auth::guard('cliente')->id();
    }

    private function resolveBultoCantidad(?ProductoTodotex $producto): int
    {
        return max(1, (int) ($producto?->bulto_cantidad ?? 1));
    }

    private function resolveDescuento(int $productoId): float
    {
        return $this->descuentoBase;
    }

    private function resolveDescuentoPersonalizado(int $productoId): float
    {
        return $this->descuentosPersonalizados[$productoId] ?? 0;
    }

    // ─── Contadores de presentaciones ─────────────────────────────────────────────
    public function incrementar($productoId): void
    {
        $producto      = ProductoTodotex::select('id', 'bulto_cantidad')->find($productoId);
        $bultoCantidad = $this->resolveBultoCantidad($producto);

        $this->cantidades[$productoId] = ($this->cantidades[$productoId] ?? 0) + $bultoCantidad;
    }

    public function decrementar($productoId): void
    {
        $producto      = ProductoTodotex::select('id', 'bulto_cantidad')->find($productoId);
        $bultoCantidad = $this->resolveBultoCantidad($producto);
        $actual        = $this->cantidades[$productoId] ?? 0;

        $this->cantidades[$productoId] = max(0, $actual - $bultoCantidad);
    }

    public function incrementarBulto($productoId): void
    {
        $this->cantidadesBultos[$productoId] = ($this->cantidadesBultos[$productoId] ?? 0) + 1;
    }

    public function decrementarBulto($productoId): void
    {
        $actual = $this->cantidadesBultos[$productoId] ?? 0;
        if ($actual > 0) $this->cantidadesBultos[$productoId] = $actual - 1;
    }

    // ─── Filtros ──────────────────────────────────────────────────────────────────
    public function buscar(): void             { $this->resetPage(); }
    public function updatedSearch(): void      { $this->resetPage(); }
    public function updatedCategoriaId(): void { $this->resetPage(); }
    public function updatedRubroId(): void     { $this->categoriaId = null; $this->resetPage(); }
    public function updatedFamiliaId(): void   { $this->rubroId = null; $this->categoriaId = null; $this->resetPage(); }
    public function limpiarFiltros(): void     { $this->reset(['search', 'familiaId', 'categoriaId', 'rubroId']); $this->resetPage(); }
    public function updatedPerPage(): void     { $this->resetPage(); }

    // ─── Modal bulto personalizado ─────────────────────────────────────────────────
    public function abrirBultoModal(int $productoId): void
    {
        $producto = ProductoTodotex::select('id', 'codigo', 'codigo_color', 'bulto_cantidad')->find($productoId);

        if (!$producto || !$producto->codigo_color) return;

        $baseCode  = substr((string) $producto->codigo, 0, -4);
        $variantes = ProductoTodotex::where('visible', true)
            ->where('codigo', 'like', $baseCode . '%')
            ->whereNotNull('codigo_color')
            ->with('gallery')
            ->orderBy('nombre_color')
            ->get();

        if ($variantes->count() < 2) {
            $this->dispatch('producto-agregado', [
                'message' => 'Este producto no tiene otras variantes de color disponibles.',
                'type'    => 'error',
            ]);
            return;
        }

        $data = $variantes->map(function ($v) {
            $portada = $v->gallery->firstWhere('portada', true) ?? $v->gallery->first();
            return [
                'id'           => $v->id,
                'codigo'       => $v->codigo,
                'codigo_color' => $v->codigo_color,
                'nombre_color' => $v->nombre_color ?? $v->codigo_color,
                'imagen'       => $portada ? Storage::url($portada->image) : null,
            ];
        })->values()->toArray();

        $this->dispatch('abrir-bulto-modal', [
            'productoId'    => $productoId,
            'bultoNombre'   => $producto->codigo,
            'bultoCantidad' => max(1, (int) $producto->bulto_cantidad),
            'variantes'     => $data,
        ]);
    }

    public function agregarBultoPersonalizado(int $productoId, array $distribuciones): void
    {
        $clienteId = $this->resolveClienteId();

        if (!$clienteId) {
            $this->dispatch('producto-agregado', ['message' => 'Seleccioná un cliente primero.', 'type' => 'error']);
            return;
        }

        $productoRef   = ProductoTodotex::select('id', 'bulto_cantidad')->find($productoId);
        $bultoCantidad = max(1, (int) $productoRef?->bulto_cantidad);
        $totalUnidades = array_sum(array_column($distribuciones, 'cantidad'));

        if ($totalUnidades <= 0 || ($totalUnidades % $bultoCantidad) !== 0) {
            $this->dispatch('producto-agregado', [
                'message' => "El total debe ser múltiplo de {$bultoCantidad} unidades.",
                'type'    => 'error',
            ]);
            return;
        }

        foreach ($distribuciones as $item) {
            $cantidad = (int) ($item['cantidad'] ?? 0);
            if ($cantidad <= 0) continue;

            $variante = ProductoTodotex::find((int) $item['id']);
            if (!$variante) continue;

            $precio    = floatval($variante->precio_unitario ?? 0);
            $descuento = $this->resolveDescuento($variante->id);
            $descuentoPersonalizado = $this->resolveDescuentoPersonalizado($variante->id);

            $itemCarrito = Carrito::where('cliente_id', $clienteId)
                ->where('producto_todotex_id', $variante->id)
                ->first();

            if ($itemCarrito) {
                $itemCarrito->cantidad_presentacion = max(0, (int) ($itemCarrito->cantidad_presentacion ?? 0)) + $cantidad;
                $itemCarrito->precio_unitario       = $precio;
                $itemCarrito->descuento_unitario    = $descuento;
                $itemCarrito->descuento_personalizado = $descuentoPersonalizado;
                $itemCarrito->syncCantidadTotal($bultoCantidad);
                $itemCarrito->save();
            } else {
                $nuevo = new Carrito([
                    'cliente_id'            => $clienteId,
                    'producto_todotex_id'   => $variante->id,
                    'cantidad_presentacion' => $cantidad,
                    'cantidad_bultos'       => 0,
                    'precio_unitario'       => $precio,
                    'descuento_unitario'    => $descuento,
                    'descuento_personalizado' => $descuentoPersonalizado,
                ]);
                $nuevo->syncCantidadTotal($bultoCantidad);
                $nuevo->save();
            }
        }

        $this->carritoTieneItems = true;
        $this->dispatch('producto-agregado', ['message' => 'Bulto personalizado agregado al carrito.', 'type' => 'success']);
        $this->dispatch('carrito-actualizado');
    }

    public function agregarAlCarrito($productoId): void
    {
        $clienteId = $this->resolveClienteId();

        if (!$clienteId) {
            $this->dispatch('producto-agregado', ['message' => 'Seleccioná un cliente primero.', 'type' => 'error']);
            return;
        }

        $producto = ProductoTodotex::find($productoId);
        if (!$producto) {
            $this->dispatch('producto-agregado', ['message' => 'Producto no encontrado.', 'type' => 'error']);
            return;
        }

        $bultoCantidad        = $this->resolveBultoCantidad($producto);
        $cantidadPresentacion = max(0, (int) ($this->cantidades[$productoId] ?? 0));
        $cantidadBultos       = max(0, (int) ($this->cantidadesBultos[$productoId] ?? 0));
        $cantidadTotal        = $cantidadPresentacion + ($cantidadBultos * $bultoCantidad);

        if ($cantidadTotal <= 0) {
            $this->dispatch('producto-agregado', ['message' => 'Elegí una cantidad antes de agregar al carrito.', 'type' => 'error']);
            return;
        }

        $precio    = floatval($producto->precio_unitario ?? 0);
        $descuento = $this->resolveDescuento($productoId);
        $descuentoPersonalizado = $this->resolveDescuentoPersonalizado($productoId);

        $itemCarrito = Carrito::where('cliente_id', $clienteId)
            ->where('producto_todotex_id', $productoId)
            ->first();

        if ($itemCarrito) {
            $itemCarrito->cantidad_presentacion = max(0, (int) ($itemCarrito->cantidad_presentacion ?? $itemCarrito->cantidad ?? 0)) + $cantidadPresentacion;
            $itemCarrito->cantidad_bultos       = max(0, (int) ($itemCarrito->cantidad_bultos ?? 0)) + $cantidadBultos;
            $itemCarrito->precio_unitario       = $precio;
            $itemCarrito->descuento_unitario    = $descuento;
            $itemCarrito->descuento_personalizado = $descuentoPersonalizado;
            $itemCarrito->syncCantidadTotal($bultoCantidad);
            $itemCarrito->save();
            $mensaje = "{$producto->codigo} actualizado en el carrito.";
        } else {
            $itemCarrito = new Carrito([
                'cliente_id'            => $clienteId,
                'producto_todotex_id'   => $productoId,
                'cantidad_presentacion' => $cantidadPresentacion,
                'cantidad_bultos'       => $cantidadBultos,
                'precio_unitario'       => $precio,
                'descuento_unitario'    => $descuento,
                'descuento_personalizado' => $descuentoPersonalizado,
            ]);
            $itemCarrito->syncCantidadTotal($bultoCantidad);
            $itemCarrito->save();
            $mensaje = "{$producto->codigo} agregado al carrito.";
        }

        $this->carritoTieneItems = true;
        $this->dispatch('producto-agregado', ['message' => $mensaje, 'type' => 'success']);
        $this->dispatch('carrito-actualizado');
    }

    public function render()
    {
        $familias = Familia::where('visible', true)->orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')->get();

        $categorias = CategoriaTodotex::query()
            ->where('visible', true)
            ->when($this->familiaId, fn ($q) => $q->where('familia_id', $this->familiaId))
            ->when($this->rubroId, fn ($q) => $q->whereHas('rubros', fn ($q2) => $q2->where('id', $this->rubroId)))
            ->orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)')
            ->get();

        $rubros = Rubro::query()
            ->when($this->familiaId, fn ($q) =>
                $q->whereHas('categorias', fn ($q2) => $q2->where('familia_id', $this->familiaId))
            )
            ->orderBy('titulo')
            ->get();

        $query = ProductoTodotex::where('visible', true)
            ->with(['gallery', 'categorias.familia']);

        if ($this->rubroId) {
            $rubroId = $this->rubroId;
            $query->leftJoin('rubro_producto_orden as rpo', function ($join) use ($rubroId) {
                $join->on('rpo.producto_id', '=', 'productos_todotex.id')
                     ->where('rpo.rubro_id', '=', $rubroId);
            })
            ->select('productos_todotex.*')
            ->orderByRaw("rpo.orden IS NULL, CAST(rpo.orden AS UNSIGNED), CAST(REPLACE(productos_todotex.codigo, ' ', '') AS UNSIGNED)")
            ->whereHas('categorias', fn ($q) => $q->where('visible', true)->whereHas('rubros', fn ($q2) => $q2->where('id', $rubroId)));
        } elseif ($this->categoriaId) {
            $categoriaId = $this->categoriaId;
            $query->join('categoria_producto as cp_ord', function ($join) use ($categoriaId) {
                $join->on('cp_ord.producto_id', '=', 'productos_todotex.id')
                     ->where('cp_ord.categoria_id', '=', $categoriaId);
            })
            ->select('productos_todotex.*')
            ->orderByRaw("cp_ord.orden IS NULL, CAST(cp_ord.orden AS UNSIGNED), CAST(REPLACE(productos_todotex.codigo, ' ', '') AS UNSIGNED)");
        } else {
            $query->orderByRaw('orden IS NULL, CAST(orden AS UNSIGNED)');
        }

        if ($this->familiaId) {
            $query->whereHas('categorias', fn ($q) => $q->where('familia_id', $this->familiaId)->where('visible', true));
        }
        if ($this->categoriaId && !$this->rubroId) {
            $query->whereHas('categorias', fn ($q) => $q->where('categorias_todotex.id', $this->categoriaId)->where('visible', true));
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('codigo', 'like', "%{$this->search}%")
                  ->orWhere('descripcion', 'like', "%{$this->search}%");
            });
        }

        $productos = $query->paginate($this->perPage);

        foreach ($productos as $producto) {
            if (!isset($this->cantidades[$producto->id]))       $this->cantidades[$producto->id]       = 0;
            if (!isset($this->cantidadesBultos[$producto->id])) $this->cantidadesBultos[$producto->id] = 0;
        }

        return view('livewire.zona.productos-zona', [
            'familias'              => $familias,
            'categorias'            => $categorias,
            'rubros'                => $rubros,
            'productos'             => $productos,
            'descuentoBase'         => $this->descuentoBase,
            'descuentosPersonalizados' => $this->descuentosPersonalizados,
            'esVendedor'            => $this->esVendedor,
            'clientesLista'         => $this->clientesLista,
            'clienteSeleccionadoId' => $this->clienteSeleccionadoId,
            'clienteData'           => $this->clienteData,
            'operandoEnNombreDe'    => $this->operandoEnNombreDe,
            'vendedorLogueadoNombre'  => $this->vendedorLogueadoNombre,
            'vendedorOperativoNombre' => $this->vendedorOperativoNombre,
            'carritoTieneItems'       => $this->carritoTieneItems,
        ]);
    }
}
