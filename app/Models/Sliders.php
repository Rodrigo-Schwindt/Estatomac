<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sliders extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'image',
        'orden',
        'url'
    ];

    public function run(): void
    {
        Sliders::factory()->count(5)->create();
    }
}