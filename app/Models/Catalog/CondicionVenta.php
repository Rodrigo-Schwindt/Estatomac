<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class CondicionVenta extends Model
{
    protected $table = 'condiciones_ventas';

    protected $fillable = ['codigo', 'nombre'];
}
