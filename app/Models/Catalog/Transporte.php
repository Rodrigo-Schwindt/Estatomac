<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class Transporte extends Model
{
    protected $table = 'transportes';

    protected $fillable = ['codigo', 'nombre'];
}
