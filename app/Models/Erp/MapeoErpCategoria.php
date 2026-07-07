<?php

namespace App\Models\Erp;

use App\Models\CategoriaTodotex;
use App\Models\Familia;
use Illuminate\Database\Eloquent\Model;

class MapeoErpCategoria extends Model
{
    protected $table = 'mapeos_erp_categoria';

    public const TIPO_FAMILIA    = 'familia';
    public const TIPO_SUBFAMILIA = 'subfamilia';

    protected $fillable = [
        'entidad_tipo',
        'entidad_pk_externa',
        'familia_id',
        'categoria_id',
    ];

    public function familia()
    {
        return $this->belongsTo(Familia::class, 'familia_id');
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaTodotex::class, 'categoria_id');
    }
}
