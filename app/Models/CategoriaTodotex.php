<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaTodotex extends Model
{
    use HasFactory;

    protected $table = 'categorias_todotex';

    protected $fillable = [
        'titulo',
        'orden',
        'visible',
        'familia_id',
        'rubro_id',
        'imagen',
        'fusionada_en_id',
    ];

    protected $casts = [
        'visible' => 'boolean',
    ];

    public function familia()
    {
        return $this->belongsTo(Familia::class, 'familia_id');
    }

    public function rubro()
    {
        return $this->belongsTo(Rubro::class, 'rubro_id');
    }

    public function rubros()
    {
        return $this->belongsToMany(Rubro::class, 'categorias_todotex_rubros', 'categoria_todotex_id', 'rubro_id');
    }

    public function fusionadaEn()
    {
        return $this->belongsTo(CategoriaTodotex::class, 'fusionada_en_id');
    }

    public function productos()
    {
        return $this->belongsToMany(ProductoTodotex::class, 'categoria_producto', 'categoria_id', 'producto_id');
    }
}
