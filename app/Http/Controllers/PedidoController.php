<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PedidoController extends Controller
{
    public function descargar(Pedido $pedido)
    {
        if ($pedido->cliente_id !== Auth::guard('cliente')->id()) {
            abort(403, 'No tienes permiso para descargar este pedido');
        }

        $pedido->load(['items.producto', 'cliente']);

        $pdf = Pdf::loadView('pdfs.pedido', compact('pedido'));

        return $pdf->download('pedido-' . $pedido->numero_pedido . '.pdf');
    }
}