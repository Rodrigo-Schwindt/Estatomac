<?php

namespace App\Models\Erp;

use Illuminate\Database\Eloquent\Model;

class ErpSubfamilia extends Model
{
    protected $table = 'erp_subfamilias';
    protected $fillable = ['pk_externa', 'nombre', 'familias_pk_externa'];

    public function erpFamilia()
    {
        return $this->belongsTo(ErpFamilia::class, 'familias_pk_externa', 'pk_externa');
    }
}
