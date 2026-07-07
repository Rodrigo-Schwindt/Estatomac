<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Vendedor extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'vendedores';

    protected $fillable = [
        'pk_externa',
        'codigo_externo',
        'nombre',
        'telefono',
        'celular',
        'whatsapp',
        'email',
        'activo',
        'comision',
        'opera_como',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'activo'     => 'boolean',
        'comision'   => 'decimal:2',
        'password'   => 'hashed',
        'opera_como' => 'integer',
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    /** El vendedor titular en cuyo nombre opera este vendedor. */
    public function operaComoVendedor()
    {
        return $this->belongsTo(Vendedor::class, 'opera_como');
    }
}
