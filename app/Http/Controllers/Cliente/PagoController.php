<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PagoMail;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact; // <--- 1. Importamos el modelo

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
                'archivo_path' => $path, 
                'archivo_nombre' => $request->file('archivo')->getClientOriginalName(),
                'archivo_mime' => $request->file('archivo')->getMimeType(),
            ];

            // --- CAMBIO AQUÍ ---
            
            // 2. Buscamos el primer registro de la tabla Contact
            $contacto = Contact::first();

            // 3. Definimos el destinatario:
            // Si existe el contacto en BD y tiene 'mail_adm', usamos ese.
            // Si no, usamos el del .env como respaldo para que no falle.
            $destinatario = ($contacto && $contacto->mail_adm) 
                            ? $contacto->mail_adm 
                            : config('mail.from.address');

            // 4. Enviamos al destinatario obtenido de la BD
            Mail::to($destinatario)->send(new PagoMail($datosMail));

            // -------------------

            return back()->with('success', 'Comprobante de pago enviado exitosamente');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar: ' . $e->getMessage());
        }
    }
}