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
        $pedido = Pedido::with(['cliente', 'items.producto'])
            ->where('id', $pedidoId)
            ->where('cliente_id', Auth::guard('cliente')->id())
            ->firstOrFail();

        if (!$pedido->descarga_habilitada) {
            return back()->with('error', 'La descarga de esta factura aún no está habilitada.');
        }

        $pdf = Pdf::loadView('pdf.factura', [
            'pedido' => $pedido,
        ]);

        return $pdf->download('factura-' . $pedido->numero_pedido . '.pdf');
    }
}