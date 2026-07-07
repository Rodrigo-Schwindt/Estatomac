<?php

namespace App\Http\Middleware;

use App\Models\Parametro;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMantenimiento
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $enMantenimiento = cache()->remember('parametros.en_mantenimiento', 60, function () {
                return Parametro::config()->en_mantenimiento;
            });

            if ($enMantenimiento) {
                return response()->view('mantenimiento', [], 503);
            }
        } catch (\Throwable) {
            // Si falla la DB (ej: tabla no existe aún), no bloqueamos
        }

        return $next($request);
    }
}
