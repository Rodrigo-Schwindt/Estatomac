<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_pedido',
        'cliente_id',
        'forma_pago',
        'mensaje',
        'archivo_path',
        'archivo_nombre',
        'subtotal_sin_descuento',
        'descuentos',
        'porcentaje_descuento',
        'subtotal',
        'porcentaje_iva',
        'iva',
        'total',
        'fecha_compra',
        'fecha_entrega',
        'entregado',
        'fecha_entregado',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_entrega' => 'date',
        'fecha_entregado' => 'date',
        'entregado' => 'boolean',
        'subtotal_sin_descuento' => 'decimal:2',
        'descuentos' => 'decimal:2',
        'porcentaje_descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'porcentaje_iva' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public static function generarNumeroPedido()
    {
        $ultimoPedido = static::latest('id')->first();
        $numero = $ultimoPedido ? intval(substr($ultimoPedido->numero_pedido, 0)) + 1 : 1;
        return str_pad($numero, 8, '0', STR_PAD_LEFT);
    }

    public function marcarComoEntregado()
    {
        $this->update([
            'entregado' => true,
            'fecha_entregado' => now(),
        ]);
    }
}