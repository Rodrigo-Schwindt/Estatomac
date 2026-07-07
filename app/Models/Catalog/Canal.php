<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class Canal extends Model
{
    protected $table = 'canales';

    protected $fillable = [
        'pk_externa',
        'codigo',
        'canal',
        'descuento_canal',
        'supervisor_pct',
        'vendedor_pct',
        'supervisor1_pct',
        'supervisor2_pct',
    ];

    protected $casts = [
        'descuento_canal' => 'decimal:2',
    ];
}
