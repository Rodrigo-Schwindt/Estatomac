<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'codigo_producto',
        'nombre_producto',
        'cantidad',
        'precio_unitario',
        'descuento_unitario',
        'descuento_base_porcentaje',
        'descuento_personalizado_porcentaje',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'descuento_unitario' => 'decimal:2',
        'descuento_base_porcentaje' => 'decimal:2',
        'descuento_personalizado_porcentaje' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function producto()
    {
        return $this->belongsTo(ProductoTodotex::class, 'producto_id');
    }
}
