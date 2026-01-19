<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PedidoController extends Controller
{
    public function descargar(Pedido $pedido)
    {
        // Verificar que el pedido pertenece al cliente autenticado
        if ($pedido->cliente_id !== Auth::guard('cliente')->id()) {
            abort(403, 'No autorizado');
        }

        // Cargar relaciones necesarias
        $pedido->load('items.producto', 'cliente');

        // Generar el PDF
        $pdf = Pdf::loadView('pdfs.pedido', [
            'pedido' => $pedido
        ]);

        // Descargar el PDF
        return $pdf->download('pedido-' . $pedido->numero_pedido . '.pdf');
    }
}