<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class NovCategories extends Model 
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'orden',
    ];

    public function novedades(): BelongsToMany
    {
        return $this->belongsToMany(Novedades::class, 'nov_pivote', 'category_id', 'novedades_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('orden');
    }
}