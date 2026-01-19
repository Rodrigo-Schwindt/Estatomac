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
        $validated = $request->validate([
            'forma_pago' => 'required|in:contado,transferencia,cuenta_corriente',
            'mensaje' => 'nullable|string',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $items = Carrito::with('producto')->where('cliente_id', Auth::guard('cliente')->id())->get();
        
        if ($items->isEmpty()) {
            return back()->with('toast', [
                'message' => 'El carrito está vacío',
                'type' => 'error'
            ]);
        }

        $config = CarritoConfig::first();
        $cliente = Auth::guard('cliente')->user();

        $subtotalSinDescuento = $items->sum(function ($item) {
            return $item->precio_unitario * $item->cantidad;
        });

        $descuentos = $items->sum(function ($item) {
            return $item->descuento_unitario * $item->cantidad;
        });

        $subtotal = $subtotalSinDescuento - $descuentos;
        
        $descuentoPorPago = 0;
        if ($config) {
            if ($validated['forma_pago'] === 'contado') {
                $descuentoPorPago = $subtotal * ($config->contado / 100);
            } elseif ($validated['forma_pago'] === 'transferencia') {
                $descuentoPorPago = $subtotal * ($config->transferencia / 100);
            } elseif ($validated['forma_pago'] === 'cuenta_corriente') {
                $descuentoPorPago = $subtotal * ($config->corriente / 100);
            }
        }

        $totalDescuentos = $descuentos + $descuentoPorPago;
        $porcentajeTotalDescuento = $subtotalSinDescuento > 0 ? ($totalDescuentos / $subtotalSinDescuento) * 100 : 0;
        $subtotalConDescuentoPago = $subtotal - $descuentoPorPago;
        $porcentajeIva = $config ? $config->iva : 21;
        $iva = $subtotalConDescuentoPago * ($porcentajeIva / 100);
        $total = $subtotalConDescuentoPago + $iva;

        DB::beginTransaction();
        
        try {
            $archivePath = null;
            $archivoNombre = null;
            
            if ($request->hasFile('archivo')) {
                $nombreOriginal = $request->file('archivo')->getClientOriginalName();
                $path = $request->file('archivo')->store('pedidos', 'public');
                $archivePath = $path;
                $archivoNombre = $nombreOriginal;
            }

            $pedido = Pedido::create([
                'numero_pedido' => Pedido::generarNumeroPedido(),
                'cliente_id' => $cliente->id,
                'forma_pago' => $validated['forma_pago'],
                'mensaje' => $validated['mensaje'] ?? null,
                'archivo_path' => $archivePath,
                'archivo_nombre' => $archivoNombre,
                'subtotal_sin_descuento' => $subtotalSinDescuento,
                'descuentos' => $totalDescuentos,
                'porcentaje_descuento' => $porcentajeTotalDescuento,
                'subtotal' => $subtotalConDescuentoPago,
                'porcentaje_iva' => $porcentajeIva,
                'iva' => $iva,
                'total' => $total,
                'fecha_compra' => now(),
                'fecha_entrega' => now()->addDays(7),
                'entregado' => false,
            ]);

            foreach ($items as $item) {
                $precioFinal = $item->precio_unitario - $item->descuento_unitario;
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item->producto_id,
                    'codigo_producto' => $item->producto->code,
                    'nombre_producto' => $item->producto->title,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->precio_unitario,
                    'descuento_unitario' => $item->descuento_unitario,
                    'subtotal' => $precioFinal * $item->cantidad,
                ]);
            }

            $productos = $items->map(function ($item) {
                $precioFinal = $item->precio_unitario - $item->descuento_unitario;
                return [
                    'codigo' => $item->producto->code,
                    'nombre' => $item->producto->title,
                    'precio_unitario' => $precioFinal,
                    'cantidad' => $item->cantidad,
                    'subtotal' => $precioFinal * $item->cantidad,
                ];
            })->toArray();

            $emailData = [
                'numero_pedido' => $pedido->numero_pedido,
                'cliente' => [
                    'nombre' => $cliente->nombre,
                    'email' => $cliente->email,
                    'telefono' => $cliente->telefono,
                    'domicilio' => $cliente->domicilio,
                    'localidad' => $cliente->localidad,
                    'provincia' => $cliente->provincia,
                ],
                'forma_pago' => $validated['forma_pago'],
                'productos' => $productos,
                'resumen' => [
                    'subtotal_sin_descuento' => $subtotalSinDescuento,
                    'descuentos' => $totalDescuentos,
                    'porcentaje_descuento' => $porcentajeTotalDescuento,
                    'subtotal' => $subtotalConDescuentoPago,
                    'porcentaje_iva' => $porcentajeIva,
                    'iva' => $iva,
                    'total' => $total,
                ],
                'mensaje' => $validated['mensaje'] ?? null, 
            ];

            if ($archivePath) {
                $fullPath = Storage::disk('public')->path($archivePath);
                $emailData['archivo_path'] = $fullPath;
                $emailData['archivo_nombre'] = $archivoNombre;
                $emailData['archivo_mime'] = $request->file('archivo')->getMimeType();
            }

            Mail::to(config('mail.from.address'))->send(new PedidoMail($emailData));
            
            Carrito::where('cliente_id', Auth::guard('cliente')->id())->delete();
            
            DB::commit();
            
            // 🎉 CAMBIO AQUÍ: Usar formato toast para redirect
            return redirect()->route('cliente.productos')
                ->with('toast', [
                    'message' => '¡Pedido realizado exitosamente!',
                    'type' => 'success'
                ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            // 🎉 CAMBIO AQUÍ: Usar formato toast para error
            return back()->with('toast', [
                'message' => 'Error al procesar el pedido: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
}