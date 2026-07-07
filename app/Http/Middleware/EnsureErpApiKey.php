<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureErpApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        // Modo desarrollo: API abierta. Acordate de volver a habilitar antes de prod.
        if (config('services.erp.auth_enabled') === false) {
            return $next($request);
        }

        $expected = (string) config('services.erp.api_key');

        if ($expected === '') {
            return response()->json([
                'error'   => 'server_misconfigured',
                'message' => 'ERP API key is not configured on the server.',
            ], 500);
        }

        $provided = $request->header('X-API-Key', '');

        if (!is_string($provided) || $provided === '' || !hash_equals($expected, $provided)) {
            return response()->json([
                'error'   => 'unauthorized',
                'message' => 'Invalid or missing X-API-Key header.',
            ], 401);
        }

        return $next($request);
    }
}
