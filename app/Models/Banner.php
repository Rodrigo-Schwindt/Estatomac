<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'section',
        'title',
        'image_banner',
    ];

    public function scopeForSection($query, string $section)
    {
        return $query->where('section', $section);
    }
}