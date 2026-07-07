<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $rolesPermitidos = ['admin', 'user'];

        if (!in_array(Auth::user()->role, $rolesPermitidos)) {
            abort(403, 'No tenés permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}