<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Metadata extends Model
{
    protected $fillable = [
        'section',
        'keywords',
        'description',
    ];

    public static function getForSection($section)
    {
        return static::where('section', $section)->first();
    }

    public static function getForProduct($productId)
    {
        return static::where('section', 'producto-' . $productId)->first();
    }

    public static function getForCategoria($categoriaId)
    {
        return static::where('section', 'categoria-' . $categoriaId)->first();
    }

    public static function getForNovedad($novedadId)
    {
        return static::where('section', 'novedad-' . $novedadId)->first();
    }
}