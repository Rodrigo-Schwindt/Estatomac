<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'order',
        'image',
        'destacado',
    ];

    public function subcategorias()
    {
        return $this->hasMany(Subcategoria::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}