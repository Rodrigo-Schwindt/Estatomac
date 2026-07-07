<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoGallery extends Model
{
    use HasFactory;

    protected $table = 'producto_gallery';

    protected $fillable = [
        'producto_id',
        'image',
        'portada',
    ];

    public function producto()
    {
        return $this->belongsTo(ProductoTodotex::class, 'producto_id');
    }
}
