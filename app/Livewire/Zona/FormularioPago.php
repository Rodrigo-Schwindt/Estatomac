<?php

namespace App\Livewire\Zona;

use Livewire\Component;
use App\Models\CarritoConfig;
use Illuminate\Support\Facades\Mail;
use App\Mail\PagoMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.zone')]
class FormularioPago extends Component
{
    public $fecha;
    public $importe;
    public $banco;
    public $sucursal;
    public $facturas;
    public $observaciones;

    protected $rules = [
        'fecha' => 'required|date',
        'importe' => 'required|numeric|min:0',
        'banco' => 'required|string|max:255',
        'sucursal' => 'required|string|max:255',
        'facturas' => 'nullable|string',
        'observaciones' => 'nullable|string',
    ];

    protected $messages = [
        'fecha.required' => 'La fecha es obligatoria',
        'importe.required' => 'El importe es obligatorio',
        'importe.numeric' => 'El importe debe ser un número',
        'banco.required' => 'El banco es obligatorio',
        'sucursal.required' => 'La sucursal es obligatoria',
    ];

    public function render()
    {
        return view('livewire.zona.formulario-pago', [
            'config' => CarritoConfig::first()
        ]);
    }
}