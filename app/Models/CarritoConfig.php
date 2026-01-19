<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarritoConfig extends Model
{
    use HasFactory;

    protected $table = 'carrito_config';

    protected $fillable = [
        'title',
        'description',
        'title2',
        'description2',
        'informacion',
        'escribenos',
        'contado',
        'transferencia',
        'corriente',
        'iva',
    ];

    protected $casts = [
        'contado' => 'decimal:2',
        'transferencia' => 'decimal:2',
        'corriente' => 'decimal:2',
        'iva' => 'decimal:2',
    ];
}