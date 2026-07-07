<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Precio extends Model
{
    use HasFactory;

    protected $table = 'precios';

    protected $fillable = [
        'pk_externa',
        'numero',
        'title',
        'fecha_desde',
        'vigente_sn',
        'observaciones',
        'archivo',
    ];

    protected $casts = [
        'numero'      => 'integer',
        'fecha_desde' => 'date',
        'vigente_sn'  => 'boolean',
    ];

    public function scopeVigente(Builder $query): Builder
    {
        return $query->where('vigente_sn', true);
    }

    public static function activa(): ?self
    {
        return static::query()->vigente()->orderByDesc('fecha_desde')->first();
    }

    public function activar(): void
    {
        DB::transaction(function () {
            static::query()->where('id', '!=', $this->id)->update(['vigente_sn' => false]);
            $this->vigente_sn = true;
            $this->save();
        });
    }
}
