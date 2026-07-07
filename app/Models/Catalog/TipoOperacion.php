<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class TipoOperacion extends Model
{
    protected $table = 'tipos_operaciones';

    protected $fillable = ['codigo', 'nombre'];
}
