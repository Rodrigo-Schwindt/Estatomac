<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarritoConfig extends Model
{
    use HasFactory;

    protected $table = 'carrito_config';

    protected $fillable = [
        'title',
        'description',
        'title2',
        'description2',
        'informacion',
        'escribenos',
        'contado',
        'contado_activo',
        'transferencia',
        'transferencia_activa',
        'corriente',
        'corriente_activa',
        'iva',
        'entrega_1_label',
        'entrega_1_costo',
        'entrega_1_activa',
        'entrega_2_label',
        'entrega_2_costo',
        'entrega_2_activa',
        'entrega_3_label',
        'entrega_3_costo',
        'entrega_3_activa',
    ];

    protected $casts = [
        'contado' => 'decimal:2',
        'contado_activo' => 'boolean',
        'transferencia' => 'decimal:2',
        'transferencia_activa' => 'boolean',
        'corriente' => 'decimal:2',
        'corriente_activa' => 'boolean',
        'iva' => 'decimal:2',
        'entrega_1_costo' => 'decimal:2',
        'entrega_1_activa' => 'boolean',
        'entrega_2_costo' => 'decimal:2',
        'entrega_2_activa' => 'boolean',
        'entrega_3_costo' => 'decimal:2',
        'entrega_3_activa' => 'boolean',
    ];
}
