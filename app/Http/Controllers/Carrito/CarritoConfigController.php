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
            'entrega_1_costo' => 'required|numeric|min:0',
            'entrega_2_costo' => 'required|numeric|min:0',
            'entrega_3_costo' => 'required|numeric|min:0',
        ]);

        $config = CarritoConfig::first() ?: new CarritoConfig();

        $config->title = $request->input('title');
        $config->description = $request->input('description');
        $config->title2 = $request->input('title2');
        $config->description2 = $request->input('description2');
        $config->informacion = $request->input('informacion');
        $config->escribenos = $request->input('escribenos');
        $config->contado = $request->input('contado');
        $config->contado_activo = $request->boolean('contado_activo');
        $config->transferencia = $request->input('transferencia');
        $config->transferencia_activa = $request->boolean('transferencia_activa');
        $config->corriente = $request->input('corriente');
        $config->corriente_activa = $request->boolean('corriente_activa');
        $config->iva = $request->input('iva');
        $config->entrega_1_label = $request->input('entrega_1_label');
        $config->entrega_1_costo = $request->input('entrega_1_costo');
        $config->entrega_1_activa = $request->boolean('entrega_1_activa');
        $config->entrega_2_label = $request->input('entrega_2_label');
        $config->entrega_2_costo = $request->input('entrega_2_costo');
        $config->entrega_2_activa = $request->boolean('entrega_2_activa');
        $config->entrega_3_label = $request->input('entrega_3_label');
        $config->entrega_3_costo = $request->input('entrega_3_costo');
        $config->entrega_3_activa = $request->boolean('entrega_3_activa');

        $config->save();

        return redirect()->route('carrito.config.index')->with('success', 'Configuración guardada exitosamente');
    }
}
