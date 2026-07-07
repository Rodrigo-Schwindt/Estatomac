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
        'producto_todotex_id',
        'cantidad',
        'cantidad_presentacion',
        'cantidad_bultos',
        'precio_unitario',
        'descuento_unitario',
        'descuento_personalizado',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'cantidad_presentacion' => 'integer',
        'cantidad_bultos' => 'integer',
        'precio_unitario' => 'decimal:2',
        'descuento_unitario' => 'decimal:2',
        'descuento_personalizado' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function productoTodotex()
    {
        return $this->belongsTo(ProductoTodotex::class, 'producto_todotex_id');
    }

    public function syncCantidadTotal(?int $bultoCantidad = null): void
    {
        $bultoCantidad = max(1, $bultoCantidad ?? (int) ($this->productoTodotex?->bulto_cantidad ?? 1));
        $this->cantidad_presentacion = max(0, (int) ($this->cantidad_presentacion ?? 0));
        $this->cantidad_bultos = max(0, (int) ($this->cantidad_bultos ?? 0));
        $this->cantidad = $this->cantidad_presentacion + ($this->cantidad_bultos * $bultoCantidad);
    }

    public function getPrecioFinalAttribute()
    {
        $precio = (float) $this->precio_unitario;
        $descuento = max(0, min(100, (float) ($this->descuento_unitario ?? 0)));
        $descuentoPersonalizado = max(0, min(100, (float) ($this->descuento_personalizado ?? 0)));

        return $precio * (1 - $descuento / 100) * (1 - $descuentoPersonalizado / 100);
    }

    public function getTotalAttribute()
    {
        return $this->precio_final * $this->cantidad;
    }
}
