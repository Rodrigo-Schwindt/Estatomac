<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    public function generar($pedidoId)
    {
        $pedido = Pedido::with(['cliente', 'items.producto'])->findOrFail($pedidoId);
        
        $pdf = Pdf::loadView('pdf.factura', [
            'pedido' => $pedido,
        ]);
        
        return $pdf->download('factura-' . $pedido->numero_pedido . '.pdf');
    }
}