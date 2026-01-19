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
        'nombre',
        'email',
        'cuil',
        'cuit',
        'telefono',
        'domicilio',
        'localidad',
        'provincia',
        'activo',
        'descuento',
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
}