<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class ErpFamilia extends Model
{
    protected $table = 'erp_familias';
    protected $fillable = ['pk_externa', 'nombre'];
}
