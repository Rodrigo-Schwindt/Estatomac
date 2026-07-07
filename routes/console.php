<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Procesar ZIP del ERP cada 10 minutos
|--------------------------------------------------------------------------
| Walter sube los .zip a imports-erp/in/ (probablemente 1-2 veces por día).
| Este job los detecta y procesa automáticamente. Si no hay ZIP nuevo el
| comando sale OK sin hacer nada. Para forzar manualmente está el botón
| "Procesar ZIP del ERP" en el admin de productos.
*/
Schedule::command('imports:procesar-zip-erp')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->runInBackground();
