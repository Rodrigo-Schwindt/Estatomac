<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    use HasFactory;

    protected $table = 'carrito';

    protected $fillable = [
        'cliente_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'descuento_unitario',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'descuento_unitario' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function getPrecioFinalAttribute()
    {
        return $this->precio_unitario - $this->descuento_unitario;
    }

    public function getTotalAttribute()
    {
        return $this->precio_final * $this->cantidad;
    }
}