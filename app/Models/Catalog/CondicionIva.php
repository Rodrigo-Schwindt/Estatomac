<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class CondicionIva extends Model
{
    protected $table = 'condiciones_iva';

    protected $fillable = ['codigo', 'nombre'];
}
