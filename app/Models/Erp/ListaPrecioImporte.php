<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class ListaPrecioImporte extends Model
{
    protected $table = 'listas_precios_importes';

    protected $fillable = [
        'pk_externa',
        'listas_precios_pk_externa',
        'productos_pk_externa',
        'precio_paquete',
        'precio_unitario',
        'precio_kilo',
        'costo_producto',
    ];

    protected $casts = [
        'precio_paquete'  => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'precio_kilo'     => 'decimal:2',
        'costo_producto'  => 'decimal:2',
    ];
}
