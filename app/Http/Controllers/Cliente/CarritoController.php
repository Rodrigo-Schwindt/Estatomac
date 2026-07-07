<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Carrito;
use App\Models\CarritoConfig;
use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\PedidoMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CarritoController extends Controller
{
    public function realizarPedido(Request $request)
    {
        $config = CarritoConfig::first();

        $hayPago = $config && (
            ($config->contado_activo ?? true) ||
            ($config->transferencia_activa ?? true) ||
            ($config->corriente_activa ?? true)
        );

        $validated = $request->validate([
            'forma_pago'    => $hayPago ? 'required|in:contado,transferencia,cuenta_corriente' : 'nullable|in:contado,transferencia,cuenta_corriente',
            'forma_entrega' => 'nullable|in:entrega_1,entrega_2,entrega_3',
            'mensaje'       => 'nullable|string',
            'archivo'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Resolver cliente y vendedor según guard activo
        $esVendedor = Auth::guard('vendedor')->check();
        $vendedor   = $esVendedor ? Auth::guard('vendedor')->user() : null;

        if ($esVendedor) {
            $clienteId = session('vendedor_cliente_id');
            $cliente   = $clienteId ? \App\Models\Cliente::find($clienteId) : null;
        } else {
            $cliente   = Auth::guard('cliente')->user();
            $clienteId = $cliente?->id;
        }

        if (!$cliente) {
            return back()->with('toast', [
                'message' => $esVendedor ? 'Seleccioná un cliente antes de realizar el pedido.' : 'Sesión inválida.',
                'type' => 'error',
            ]);
        }

        $items = Carrito::with('productoTodotex')
            ->where('cliente_id', $clienteId)
            ->get();

        if ($items->isEmpty()) {
            return back()->with('toast', [
                'message' => 'El carrito está vacío',
                'type' => 'error'
            ]);
        }

        $costoEntrega = 0;
        if ($config) {
            $formaEntrega = $validated['forma_entrega'] ?? 'entrega_1';
            if ($formaEntrega === 'entrega_1') $costoEntrega = floatval($config->entrega_1_costo);
            elseif ($formaEntrega === 'entrega_2') $costoEntrega = floatval($config->entrega_2_costo);
            elseif ($formaEntrega === 'entrega_3') $costoEntrega = floatval($config->entrega_3_costo);
        }

        $subtotalSinDescuento = 0;
        $totalDescuento       = 0;

        foreach ($items as $item) {
            $precio      = floatval($item->precio_unitario);
            $descPct     = floatval($item->descuento_unitario ?? 0);
            $descPersonalizadoPct = floatval($item->descuento_personalizado ?? 0);
            $precioFinal = $precio * (1 - $descPct / 100) * (1 - $descPersonalizadoPct / 100);
            $descMonto   = $precio - $precioFinal;

            $subtotalSinDescuento += $precio * $item->cantidad;
            $totalDescuento       += $descMonto * $item->cantidad;
        }

        $subtotalConDescuentos = $subtotalSinDescuento - $totalDescuento;

        $formaPago = $validated['forma_pago'] ?? null;

        $descuentoPorPago = 0;
        if ($config && $formaPago) {
            if ($formaPago === 'contado') {
                $descuentoPorPago = $subtotalConDescuentos * ($config->contado / 100);
            } elseif ($formaPago === 'transferencia') {
                $descuentoPorPago = $subtotalConDescuentos * ($config->transferencia / 100);
            } elseif ($formaPago === 'cuenta_corriente') {
                $descuentoPorPago = $subtotalConDescuentos * ($config->corriente / 100);
            }
        }

        $totalDescuentos         = $totalDescuento + $descuentoPorPago;
        $subtotalFinal           = max(0, $subtotalConDescuentos - $descuentoPorPago);
        $porcentajeTotalDescuento = $subtotalSinDescuento > 0
            ? ($totalDescuentos / $subtotalSinDescuento) * 100
            : 0;

        $porcentajeIva = $config ? $config->iva : 21;
        $iva   = $subtotalFinal * ($porcentajeIva / 100);
        $total = $subtotalFinal + $iva + $costoEntrega;

        DB::beginTransaction();

        try {
            $archivePath  = null;
            $archivoNombre = null;

            if ($request->hasFile('archivo')) {
                $archivoNombre = $request->file('archivo')->getClientOriginalName();
                $archivePath   = $request->file('archivo')->store('pedidos', 'public');
            }

            // "Vendedor operativo" según el descriptivo:
            // - vendedor_id           = TITULAR al que se imputa la venta (el opera_como, si existe).
            // - vendedor_operativo_id = vendedor FÍSICO que cargó el pedido (solo si difiere del titular).
            // Ej: Juan opera como Pedro → vendedor_id = Pedro, vendedor_operativo_id = Juan.
            $vendedorTitularId   = null;
            $vendedorOperativoId = null;
            if ($vendedor) {
                $vendedorTitularId = ($vendedor->opera_como && $vendedor->opera_como > 0)
                    ? $vendedor->opera_como
                    : $vendedor->id;
                if ($vendedorTitularId !== $vendedor->id) {
                    $vendedorOperativoId = $vendedor->id;
                }
            }

            $pedido = Pedido::create([
                'numero_pedido'          => Pedido::generarNumeroPedido(),
                'cliente_id'             => $cliente->id,
                'vendedor_id'            => $vendedorTitularId,
                'vendedor_operativo_id'  => $vendedorOperativoId,
                'forma_pago'             => $formaPago,
                'forma_entrega'          => $validated['forma_entrega'] ?? null,
                'costo_entrega'          => $costoEntrega,
                'mensaje'                => $validated['mensaje'] ?? null,
                'archivo_path'           => $archivePath,
                'archivo_nombre'         => $archivoNombre,
                'subtotal_sin_descuento' => $subtotalSinDescuento,
                'descuentos'             => $totalDescuentos,
                'porcentaje_descuento'   => $porcentajeTotalDescuento,
                'subtotal'               => $subtotalFinal,
                'porcentaje_iva'         => $porcentajeIva,
                'iva'                    => $iva,
                'total'                  => $total,
                'fecha_compra'           => now(),
                'fecha_entrega'          => null,
                'entregado'              => false,
            ]);

            foreach ($items as $item) {
                $precio    = floatval($item->precio_unitario);
                $descPct   = floatval($item->descuento_unitario ?? 0);
                $descPersonalizadoPct = floatval($item->descuento_personalizado ?? 0);
                $precioFinal = $precio * (1 - $descPct / 100) * (1 - $descPersonalizadoPct / 100);
                $descMonto = $precio - $precioFinal;

                PedidoItem::create([
                    'pedido_id'       => $pedido->id,
                    'producto_id'     => $item->producto_todotex_id,
                    'codigo_producto' => $item->productoTodotex?->codigo ?? '-',
                    'nombre_producto' => $item->productoTodotex?->descripcion ?? $item->productoTodotex?->titulo ?? '-',
                    'cantidad'        => $item->cantidad,
                    'precio_unitario' => $precio,
                    'descuento_unitario' => $descMonto,
                    'descuento_base_porcentaje' => $descPct,
                    'descuento_personalizado_porcentaje' => $descPersonalizadoPct,
                    'subtotal'        => $precioFinal * $item->cantidad,
                ]);
            }

            // Confirmar el pedido y limpiar carrito antes de enviar el mail
            // para que un fallo de mail no impida que el pedido quede registrado
            Carrito::where('cliente_id', $clienteId)->delete();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear pedido', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Error al procesar el pedido: ' . $e->getMessage());
        }

        // Enviar mail fuera de la transacción: si falla, el pedido ya está guardado
        try {
            $productos = $items->map(function ($item) {
                $precio               = floatval($item->precio_unitario);
                $descPct              = floatval($item->descuento_unitario ?? 0);
                $descPersonalizadoPct = floatval($item->descuento_personalizado ?? 0);
                $precioFinal          = $precio * (1 - $descPct / 100) * (1 - $descPersonalizadoPct / 100);
                return [
                    'codigo'          => $item->productoTodotex?->codigo ?? '-',
                    'nombre'          => $item->productoTodotex?->descripcion ?? $item->productoTodotex?->titulo ?? '-',
                    'precio_unitario' => $precioFinal,
                    'cantidad'        => $item->cantidad,
                    'subtotal'        => $precioFinal * $item->cantidad,
                ];
            })->toArray();

            $emailData = [
                'numero_pedido' => $pedido->numero_pedido,
                'cliente' => [
                    'nombre'    => $cliente->nombre,
                    'email'     => $cliente->email,
                    'telefono'  => $cliente->telefono,
                    'domicilio' => $cliente->domicilio,
                    'localidad' => $cliente->localidad,
                    'provincia' => $cliente->provincia,
                ],
                'forma_pago' => $formaPago,
                'productos'  => $productos,
                'resumen'    => [
                    'subtotal_sin_descuento' => $subtotalSinDescuento,
                    'descuentos'             => $totalDescuentos,
                    'porcentaje_descuento'   => $porcentajeTotalDescuento,
                    'subtotal'               => $subtotalConDescuentos,
                    'porcentaje_iva'         => $porcentajeIva,
                    'iva'                    => $iva,
                    'total'                  => $total,
                ],
                'mensaje' => $validated['mensaje'] ?? null,
            ];

            if ($archivePath) {
                $emailData['archivo_path']  = Storage::disk('public')->path($archivePath);
                $emailData['archivo_nombre'] = $archivoNombre;
                $emailData['archivo_mime']  = $request->file('archivo')->getMimeType();
            }

            Mail::to(config('mail.from.address'))->send(new PedidoMail($emailData));
        } catch (\Exception $e) {
            \Log::error('Error al enviar email de pedido', [
                'pedido'  => $pedido->numero_pedido,
                'message' => $e->getMessage(),
            ]);
        }

        return redirect()->route('cliente.productos')
            ->with('toast', [
                'message' => '¡Pedido realizado exitosamente!',
                'type'    => 'success',
            ]);
    }
}
