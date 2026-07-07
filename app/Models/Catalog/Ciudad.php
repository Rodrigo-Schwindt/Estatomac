<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudades';

    protected $fillable = ['codigo', 'nombre', 'provincia', 'codigo_postal'];
}
