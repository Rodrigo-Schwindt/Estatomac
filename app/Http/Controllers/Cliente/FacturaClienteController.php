<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaClienteController extends Controller
{
public function descargar($pedidoId)
{
    try {
        $pedido = Pedido::with(['cliente', 'items.producto'])
            ->where('id', $pedidoId)
            ->where('cliente_id', Auth::guard('cliente')->id())
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.factura', [
            'pedido' => $pedido,
        ]);

        return $pdf->download('factura-' . $pedido->numero_pedido . '.pdf');
        
    } catch (\Exception $e) {
        dd($e->getMessage()); // Esto te mostrará el error exacto
    }
}
}