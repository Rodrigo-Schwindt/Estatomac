<?php

namespace App\Livewire\Zona;

use App\Models\Carrito;
use App\Models\CarritoConfig;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.zone')]
class CarritoZona extends Component
{
    public $formaPago = 'contado';
    public $formEntrega = 'entrega_1';

    public bool $esVendedor = false;
    public ?string $clienteNombre = null;

    protected $listeners = ['carrito-actualizado' => '$refresh'];

    public function mount(): void
    {
        if (Auth::guard('vendedor')->check()) {
            $this->esVendedor = true;
            $clienteId = session('vendedor_cliente_id');
            if ($clienteId) {
                $this->clienteNombre = Cliente::find($clienteId)?->nombre;
            }
            return;
        }

        if (Auth::guard('cliente')->check()) {
            return;
        }

        session()->flash('openLoginModal', true);
        $this->redirect(route('home'));
    }

    private function resolveClienteId(): ?int
    {
        if ($this->esVendedor) {
            return session('vendedor_cliente_id');
        }
        return Auth::guard('cliente')->id();
    }

    #[On('carrito-actualizado')]
    public function actualizarCarrito(): void {}

    private function resolveBultoCantidad(?Carrito $item): int
    {
        return max(1, (int) ($item?->productoTodotex?->bulto_cantidad ?? 1));
    }

    private function persistirItem(Carrito $item, int $bultoCantidad): void
    {
        $item->syncCantidadTotal($bultoCantidad);

        if ($item->cantidad <= 0) {
            $item->delete();
            return;
        }

        $item->save();
    }

    private function findItem(int $carritoId): ?Carrito
    {
        $clienteId = $this->resolveClienteId();
        $item = Carrito::with('productoTodotex:id,bulto_cantidad')->find($carritoId);
        return ($item && $item->cliente_id === $clienteId) ? $item : null;
    }

    public function incrementar($carritoId): void
    {
        $item = $this->findItem($carritoId);
        if (!$item) return;

        $bultoCantidad = $this->resolveBultoCantidad($item);
        $item->cantidad_presentacion = max(0, (int) ($item->cantidad_presentacion ?? $item->cantidad ?? 0)) + $bultoCantidad;
        $this->persistirItem($item, $bultoCantidad);
    }

    public function decrementar($carritoId): void
    {
        $item = $this->findItem($carritoId);
        if (!$item) return;

        $bultoCantidad = $this->resolveBultoCantidad($item);
        $cantidadPresentacion = max(0, (int) ($item->cantidad_presentacion ?? $item->cantidad ?? 0));

        if ($cantidadPresentacion > 0) {
            $item->cantidad_presentacion = max(0, $cantidadPresentacion - $bultoCantidad);
            $this->persistirItem($item, $bultoCantidad);
        }
    }

    public function incrementarBulto($carritoId): void
    {
        $item = $this->findItem($carritoId);
        if (!$item) return;

        $bultoCantidad = $this->resolveBultoCantidad($item);
        $item->cantidad_presentacion = max(0, (int) ($item->cantidad_presentacion ?? $item->cantidad ?? 0));
        $item->cantidad_bultos = max(0, (int) ($item->cantidad_bultos ?? 0)) + 1;
        $this->persistirItem($item, $bultoCantidad);
    }

    public function decrementarBulto($carritoId): void
    {
        $item = $this->findItem($carritoId);
        if (!$item) return;

        $bultoCantidad = $this->resolveBultoCantidad($item);
        $cantidadBultos = max(0, (int) ($item->cantidad_bultos ?? 0));

        if ($cantidadBultos > 0) {
            $item->cantidad_presentacion = max(0, (int) ($item->cantidad_presentacion ?? $item->cantidad ?? 0));
            $item->cantidad_bultos = $cantidadBultos - 1;
            $this->persistirItem($item, $bultoCantidad);
        }
    }

    public function setDescuentoPersonalizado(int $carritoId, mixed $valor): void
    {
        $item = $this->findItem($carritoId);
        if (!$item) return;

        $item->descuento_personalizado = max(0, min(100, (float) $valor));
        $item->save();
    }

    public function eliminar($carritoId): void
    {
        $clienteId = $this->resolveClienteId();
        $item = Carrito::find($carritoId);

        if ($item && $item->cliente_id === $clienteId) {
            $codigo = $item->productoTodotex->codigo ?? 'Producto';
            $item->delete();

            $this->dispatch('producto-agregado', [
                'message' => "{$codigo} eliminado del carrito.",
            ]);
        }
    }

    public function render()
    {
        $config    = CarritoConfig::first();
        $clienteId = $this->resolveClienteId();

        $items = Carrito::with(['productoTodotex.gallery', 'productoTodotex.categorias.familia'])
            ->where('cliente_id', $clienteId)
            ->get();

        // Construir opciones activas de entrega
        $opcionesEntrega = [];
        if (!$config || ($config->entrega_1_activa ?? true)) {
            $opcionesEntrega['entrega_1'] = ['label' => $config->entrega_1_label ?? 'Retiro cliente', 'costo' => floatval($config->entrega_1_costo ?? 0)];
        }
        if (!$config || ($config->entrega_2_activa ?? true)) {
            $opcionesEntrega['entrega_2'] = ['label' => $config->entrega_2_label ?? 'Reparto TodoTex', 'costo' => floatval($config->entrega_2_costo ?? 0)];
        }
        if (!$config || ($config->entrega_3_activa ?? true)) {
            $opcionesEntrega['entrega_3'] = ['label' => $config->entrega_3_label ?? 'Transporte / Expreso', 'costo' => floatval($config->entrega_3_costo ?? 0)];
        }

        // Resetear formEntrega si la opción ya no está activa
        if (!isset($opcionesEntrega[$this->formEntrega])) {
            $this->formEntrega = array_key_first($opcionesEntrega) ?? '';
        }

        // Construir opciones activas de pago
        $opcionesPago = [];
        if (!$config || ($config->contado_activo ?? true)) {
            $opcionesPago['contado'] = ['label' => 'Contado', 'descuento' => floatval($config->contado ?? 0)];
        }
        if (!$config || ($config->transferencia_activa ?? true)) {
            $opcionesPago['transferencia'] = ['label' => 'Transferencia', 'descuento' => floatval($config->transferencia ?? 0)];
        }
        if (!$config || ($config->corriente_activa ?? true)) {
            $opcionesPago['cuenta_corriente'] = ['label' => 'Cuenta corriente', 'descuento' => floatval($config->corriente ?? 0)];
        }

        // Resetear formaPago si la opción ya no está activa
        if (!isset($opcionesPago[$this->formaPago])) {
            $this->formaPago = array_key_first($opcionesPago) ?? '';
        }

        $subtotalSinDescuento        = 0;
        $totalDescuento              = 0;
        $totalDescuentoCliente       = 0;
        $totalDescuentoPersonalizado = 0;

        foreach ($items as $item) {
            $bultoCantidad = $this->resolveBultoCantidad($item);
            $item->cantidad_presentacion = max(0, (int) ($item->cantidad_presentacion ?? $item->cantidad ?? 0));
            $item->cantidad_bultos = max(0, (int) ($item->cantidad_bultos ?? 0));
            $cantidadTotal = $item->cantidad_presentacion + ($item->cantidad_bultos * $bultoCantidad);
            $item->cantidad = $cantidadTotal;

            $precioOriginal            = floatval($item->precio_unitario);
            $descuentoPct              = floatval($item->descuento_unitario ?? 0);
            $descuentoPersonalizadoPct = floatval($item->descuento_personalizado ?? 0);
            $precioConDescuentos       = $precioOriginal * (1 - $descuentoPct / 100) * (1 - $descuentoPersonalizadoPct / 100);
            $descuentoMonto            = $precioOriginal - $precioConDescuentos;

            $subtotalSinDescuento        += $precioOriginal * $cantidadTotal;
            $totalDescuento              += $descuentoMonto * $cantidadTotal;
            $totalDescuentoCliente       += $precioOriginal * ($descuentoPct / 100) * $cantidadTotal;
            $totalDescuentoPersonalizado += $precioOriginal * (1 - $descuentoPct / 100) * ($descuentoPersonalizadoPct / 100) * $cantidadTotal;
        }

        $subtotalConDescuentos = $subtotalSinDescuento - $totalDescuento;

        $descuentoPorPago = 0;
        if ($config && isset($opcionesPago[$this->formaPago])) {
            $pctPago = $opcionesPago[$this->formaPago]['descuento'];
            $descuentoPorPago = $subtotalConDescuentos * ($pctPago / 100);
        }

        $subtotalFinal = max(0, $subtotalConDescuentos - $descuentoPorPago);

        $costoEntrega = 0;
        if (isset($opcionesEntrega[$this->formEntrega])) {
            $costoEntrega = $opcionesEntrega[$this->formEntrega]['costo'];
        }

        $porcentajeIva = $config ? $config->iva / 100 : 0.21;
        $iva = $subtotalFinal * $porcentajeIva;
        $totalFinal = $subtotalFinal + $iva + $costoEntrega;

        return view('livewire.zona.carrito-zona', [
            'config'                      => $config,
            'items'                       => $items,
            'opcionesEntrega'             => $opcionesEntrega,
            'opcionesPago'                => $opcionesPago,
            'subtotalSinDescuento'        => $subtotalSinDescuento,
            'descuentos'                  => $totalDescuento,
            'descuentoCliente'            => $totalDescuentoCliente,
            'descuentoPersonalizado'      => $totalDescuentoPersonalizado,
            'descuentoPorPago'            => $descuentoPorPago,
            'subtotalConDescuentoPago'    => $subtotalFinal,
            'iva'                         => $iva,
            'costoEntrega'            => $costoEntrega,
            'total'                   => $totalFinal,
            'esVendedor'              => $this->esVendedor,
            'clienteNombre'           => $this->clienteNombre,
        ]);
    }
}
