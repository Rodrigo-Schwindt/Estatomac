<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerarFactura extends Component
{
    public function generarPDF($pedidoId)
    {
        $pedido = Pedido::with(['cliente', 'items.producto'])->findOrFail($pedidoId);
        
        $pdf = Pdf::loadView('pdf.factura', [
            'pedido' => $pedido,
        ]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'factura-' . $pedido->numero_pedido . '.pdf');
    }

    public function render()
    {
        return view('livewire.admin.generar-factura');
    }
}