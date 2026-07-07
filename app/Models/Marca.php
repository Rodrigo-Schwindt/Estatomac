<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'order',
    ];

    public function modelos()
    {
        return $this->hasMany(Modelo::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function productosMultiples()
    {
        return $this->belongsToMany(Producto::class, 'marca_producto');
    }
}