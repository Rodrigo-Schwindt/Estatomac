<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class TipoLista extends Model
{
    protected $table = 'tipos_listas';

    protected $fillable = ['codigo', 'nombre'];
}
