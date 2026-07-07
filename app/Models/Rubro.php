<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubro extends Model
{
    use HasFactory;

    protected $table = 'rubros';

    protected $fillable = [
        'titulo',
        'orden',
        'imagen',
    ];

    public function categorias()
    {
        return $this->belongsToMany(CategoriaTodotex::class, 'categorias_todotex_rubros', 'rubro_id', 'categoria_todotex_id');
    }
}
