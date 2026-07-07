<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class RubroCliente extends Model
{
    protected $table = 'rubros_clientes';

    protected $fillable = ['codigo', 'nombre'];
}
