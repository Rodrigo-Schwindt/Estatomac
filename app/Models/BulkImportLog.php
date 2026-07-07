<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkImportLog extends Model
{
    public const ESTADO_OK     = 'ok';
    public const ESTADO_ERROR  = 'error';
    public const ESTADO_VACIO  = 'vacio';

    protected $fillable = [
        'proceso',
        'archivo',
        'estado',
        'filas_procesadas',
        'mensaje',
        'detalle_errores',
        'user_id',
    ];

    protected $casts = [
        'detalle_errores'  => 'array',
        'filas_procesadas' => 'integer',
    ];
}
