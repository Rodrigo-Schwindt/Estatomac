<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Parametro;
use App\Models\Vendedor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class LoginSecurity
{
    public const TIPO_CLIENTE  = 'cliente';
    public const TIPO_VENDEDOR = 'vendedor';

    public static function rateKey(string $tipo, string $username, Request $request): string
    {
        return "b2b-login:{$tipo}:" . mb_strtolower(trim($username)) . ':' . $request->ip();
    }

    public static function isThrottled(string $key): false|int
    {
        $parametro = Parametro::config();
        $max       = max(1, (int) $parametro->int_fallidos);

        if (RateLimiter::tooManyAttempts($key, $max)) {
            return RateLimiter::availableIn($key);
        }
        return false;
    }

    public static function hit(string $key): void
    {
        $parametro = Parametro::config();
        $delay     = max(30, (int) $parametro->delay_error);
        RateLimiter::hit($key, $delay);
    }

    public static function clear(string $key): void
    {
        RateLimiter::clear($key);
    }

    public static function findCliente(string $username): ?Cliente
    {
        return Cliente::where('email', $username)
            ->orWhere('usuario', $username)
            ->orWhere('codigo', $username)
            ->first();
    }

    public static function findVendedor(string $username): ?Vendedor
    {
        return Vendedor::where('email', $username)
            ->orWhere('codigo_externo', $username)
            ->first();
    }

    public static function inactiveMessage(string $tipo, Model $user): string
    {
        $etiqueta = $tipo === self::TIPO_VENDEDOR ? 'VENDEDOR' : 'CLIENTE';
        $nombre   = $user->nombre ?? '';
        return "ATENCIÓN: EL {$etiqueta} {$nombre} NO SE ENCUENTRA ACTIVO. Por favor, diríjase a Soporte de Ventas.";
    }

    public static function necesitaCambioPassword(Model $user): bool
    {
        $parametro = Parametro::config();
        $dias      = (int) $parametro->dias_passwd;

        if ($dias <= 0) {
            return false;
        }

        if (!$user->password_changed_at) {
            $user->password_changed_at = now();
            $user->saveQuietly();
            return false;
        }

        return $user->password_changed_at->addDays($dias)->isPast();
    }

    public static function necesitaPasswordInicial(?Model $user): bool
    {
        return $user !== null && empty($user->password);
    }
}
