<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    protected $table = 'parametros';
    public $timestamps = false;

    protected $fillable = [
        'en_mantenimiento',
        'email_soporte',
        'int_fallidos',
        'delay_error',
        'dias_passwd',
        'largo_passwd',
    ];

    protected $casts = [
        'en_mantenimiento' => 'boolean',
        'int_fallidos'     => 'integer',
        'delay_error'      => 'integer',
        'dias_passwd'      => 'integer',
        'largo_passwd'     => 'integer',
    ];

    public static function config(): self
    {
        return static::firstOrCreate([], [
            'en_mantenimiento' => false,
            'email_soporte'    => null,
        ]);
    }
}
