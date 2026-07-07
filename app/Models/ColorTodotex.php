<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColorTodotex extends Model
{
    use HasFactory;

    protected $table = 'colores_todotex';

    protected $fillable = [
        'titulo',
        'codigo_color',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function productos()
    {
        return $this->hasMany(ProductoTodotex::class, 'color_todotex_id');
    }
}
