<?php

namespace App\Http\Controllers\Carrito;

use App\Http\Controllers\Controller;
use App\Models\CarritoConfig;
use Illuminate\Http\Request;

class CarritoConfigController extends Controller
{
    public function index()
    {
        $config = CarritoConfig::first();
        return view('livewire.carrito.config', compact('config'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'title2' => 'nullable|string|max:255',
            'description2' => 'nullable|string',
            'informacion' => 'nullable|string',
            'escribenos' => 'nullable|string',
            'contado' => 'required|numeric|min:0|max:100',
            'transferencia' => 'required|numeric|min:0|max:100',
            'corriente' => 'required|numeric|min:0|max:100',
            'iva' => 'required|numeric|min:0|max:100',
        ]);

        $config = CarritoConfig::first() ?: new CarritoConfig();

        $config->title = $request->input('title');
        $config->description = $request->input('description');
        $config->title2 = $request->input('title2');
        $config->description2 = $request->input('description2');
        $config->informacion = $request->input('informacion');
        $config->escribenos = $request->input('escribenos');
        $config->contado = $request->input('contado');
        $config->transferencia = $request->input('transferencia');
        $config->corriente = $request->input('corriente');
        $config->iva = $request->input('iva');
        
        $config->save();

        return redirect()->route('carrito.config.index')->with('success', 'Configuración guardada exitosamente');
    }
}