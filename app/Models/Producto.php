<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'marca_id',
        'modelo_id',
        'code',
        'categoria_id',
        'title',
        'image',
        'order',
        'visible',
        'nuevo',
        'precio',
        'descuento',
        'destacado',
        'oferta',
        'importados',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'destacado' => 'boolean',
        'oferta' => 'boolean',
        'importados' => 'boolean',
        'precio' => 'decimal:2',
        'descuento' => 'decimal:2',
    ];

    // Relaciones antiguas (mantener para compatibilidad)
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }

    // NUEVAS RELACIONES - Muchos a muchos
    public function marcas()
    {
        return $this->belongsToMany(Marca::class, 'marca_producto');
    }

    public function modelos()
    {
        return $this->belongsToMany(Modelo::class, 'modelo_producto');
    }

    // Relaciones existentes
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function subcategorias()
    {
        return $this->belongsToMany(Subcategoria::class, 'producto_subcategoria')
                    ->withPivot('valor')
                    ->withTimestamps();
    }

    public function equivalencias()
    {
        return $this->hasMany(Equivalencia::class);
    }

    public function gallery()
    {
        return $this->hasMany(Gallery::class);
    }
}