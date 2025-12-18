<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Novedades extends Model
{
    protected $fillable = [
        'image_banner',
        'title',
        'description',
        'orden',
        'image',
        'destacado',
    ];

    public function novcategories()
    {
        return $this->belongsToMany(NovCategories::class, 'nov_pivote', 'novedades_id', 'category_id');
    }

    public function scopeInCategory($query, $categoryId)
    {
        return $query->whereHas('novcategories', function ($q) use ($categoryId) {
            $q->where('nov_categories.id', $categoryId);
        });
    }
public function scopeSearch($query, $search)
{
    return $query->when($search, function ($q) use ($search) {
        $searchTerm = '%' . $search . '%';
        $q->where(function ($q2) use ($searchTerm) {
            $q2->where('title', 'like', $searchTerm)
               ->orWhere('description', 'like', $searchTerm);
        });
    });
}
    public function scopeInMonth($query, $month)
    {
    if (!$month) return $query;

    return $query->whereMonth('created_at', $month);
    }


    public function scopeOrdered($query)
    {
        return $query->orderBy('orden');
    }
}
