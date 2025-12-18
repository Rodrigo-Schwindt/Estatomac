<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Nosotros extends Model
{
    protected $table = 'nosotros';

    protected $fillable = [


        'title_home',
        'description_home',
        'image_home',


        'title',
        'description',
        'image',

        'title_1',
        'description_1',
        'image_1',

        'title_2',
        'description_2',
        'image_2',

        'title_3',
        'description_3',
        'image_3',

        'title_4',
        'description_4',
        'image_4',

        'title_text_1',
        'title_text_2',
        'text_1',
        'text_2',
    ];

}
