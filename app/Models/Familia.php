<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Familia extends Model
{
    use HasFactory;

    protected $table = 'familias';

    protected $fillable = [
        'pk_externa',
        'titulo',
        'orden',
        'destacado',
        'visible',
        'imagen',
    ];

    protected $casts = [
        'destacado' => 'boolean',
        'visible' => 'boolean',
    ];

    public function categorias()
    {
        return $this->hasMany(CategoriaTodotex::class, 'familia_id');
    }
}
