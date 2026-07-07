<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Cliente extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'usuario',
        'password',
        'codigo',
        'nombre',
        'nombre_fantasia',
        'email',
        'cuil',
        'cuit',
        'condicion_iva',
        'telefono',
        'celular',
        'whatsapp',
        'domicilio',
        'localidad',
        'codigo_postal',
        'provincia',
        'condicion_venta',
        'tipo_operacion',
        'descuento',
        'transporte',
        'vendedor_id',
        'rubro_cliente',
        'tipo_lista',
        'canal',
        'descuento_canal',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function carrito()
    {
        return $this->hasMany(Carrito::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
}
