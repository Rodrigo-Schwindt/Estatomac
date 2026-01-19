<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'categoria_id',
        'title',
        'title2',
        'order',
        'tolerancia',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_subcategoria')
                    ->withPivot('valor')
                    ->withTimestamps();
    }
}