<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PagoMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PagoController extends Controller
{
    public function enviar(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'importe' => 'required|numeric|min:0',
            'banco' => 'required|string|max:255',
            'sucursal' => 'required|string|max:255',
            'facturas' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        try {
            $path = $request->file('archivo')->store('comprobantes', 'public');
            $fullPath = storage_path('app/public/' . $path);

            $clienteData = null;
            if (Auth::guard('cliente')->check()) {
                $cliente = Auth::guard('cliente')->user();
                $clienteData = [
                    'nombre' => $cliente->nombre,
                    'email' => $cliente->email,
                ];
            }

            $datosMail = [
                'cliente' => $clienteData,
                'fecha' => $validated['fecha'],
                'importe' => $validated['importe'],
                'banco' => $validated['banco'],
                'sucursal' => $validated['sucursal'],
                'facturas' => $validated['facturas'],
                'observaciones' => $validated['observaciones'],
                'archivo_path' => $fullPath,
                'archivo_nombre' => $request->file('archivo')->getClientOriginalName(),
                'archivo_mime' => $request->file('archivo')->getMimeType(),
            ];

            Mail::to(config('mail.from.address'))->send(new PagoMail($datosMail));

            return back()->with('success', 'Comprobante de pago enviado exitosamente');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar: ' . $e->getMessage());
        }
    }
}