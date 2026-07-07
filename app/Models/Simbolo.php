<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simbolo extends Model
{
    use HasFactory;

    private const DISPLAY_MAP = [
        'TREBOL' => [
            'entity' => '&#9827;',
            'classes' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
        ],
        'DIAMANTE' => [
            'entity' => '&#9830;',
            'classes' => 'text-rose-700 bg-rose-50 border-rose-200',
        ],
        'ESTRELLA ROJA' => [
            'entity' => '&#10045;',
            'classes' => 'text-red-700 bg-red-50 border-red-200',
        ],
        'BETA' => [
            'entity' => '&szlig;',
            'classes' => 'text-sky-700 bg-sky-50 border-sky-200',
        ],
        'ESTRELLA AMARILLA' => [
            'entity' => '&#9733;',
            'classes' => 'text-amber-700 bg-amber-50 border-amber-200',
        ],
        'RUEDA' => [
            'entity' => '&#9784;',
            'classes' => 'text-slate-700 bg-slate-50 border-slate-200',
        ],
        'EQUIS' => [
            'entity' => '&#10807;',
            'classes' => 'text-violet-700 bg-violet-50 border-violet-200',
        ],
        'M' => [
            'entity' => 'm',
            'classes' => 'text-cyan-700 bg-cyan-50 border-cyan-200',
        ],
        'E' => [
            'entity' => 'e',
            'classes' => 'text-orange-700 bg-orange-50 border-orange-200',
        ],
        'CORONA' => [
            'entity' => '&#9812;',
            'classes' => 'text-yellow-700 bg-yellow-50 border-yellow-200',
        ],
        'N' => [
            'entity' => '&#8469;',
            'classes' => 'text-indigo-700 bg-indigo-50 border-indigo-200',
        ],
        'F' => [
            'entity' => '&#120125;',
            'classes' => 'text-fuchsia-700 bg-fuchsia-50 border-fuchsia-200',
        ],
        'P' => [
            'entity' => '&#8473;',
            'classes' => 'text-pink-700 bg-pink-50 border-pink-200',
        ],
    ];

    protected $table = 'simbolos';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function getDisplayEntityAttribute(): ?string
    {
        return self::DISPLAY_MAP[$this->nombre]['entity'] ?? null;
    }

    public function getDisplayClassesAttribute(): string
    {
        return self::DISPLAY_MAP[$this->nombre]['classes'] ?? 'text-slate-600 bg-slate-50 border-slate-200';
    }

    public function productos()
    {
        return $this->hasMany(ProductoTodotex::class, 'simbolo_id');
    }
}
