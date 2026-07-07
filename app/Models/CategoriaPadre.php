<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaPadre extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'order',
    ];

    public function categorias()
    {
        return $this->hasMany(Categoria::class);
    }
}