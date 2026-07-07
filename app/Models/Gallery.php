<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $table = 'gallery';

    protected $fillable = [
        'producto_id',
        'image',
        'order',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}