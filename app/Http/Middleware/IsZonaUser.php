<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsZonaUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('cliente')->check() || Auth::guard('vendedor')->check()) {
            return $next($request);
        }

        session()->flash('openLoginModal', true);

        return redirect()->route('home');
    }
}
