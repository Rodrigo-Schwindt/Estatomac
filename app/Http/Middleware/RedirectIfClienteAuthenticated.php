<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfClienteAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('cliente')->check()) {
            return redirect()->route('cliente.dashboard');
        }

        return $next($request);
    }
}