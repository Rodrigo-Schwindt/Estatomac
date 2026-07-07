<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LoginSecurity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendedorAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('vendedor')->check()) {
            return redirect()->route('cliente.productos');
        }

        return view('auth.vendedor-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        $key = LoginSecurity::rateKey(LoginSecurity::TIPO_VENDEDOR, $request->email, $request);

        if ($segundos = LoginSecurity::isThrottled($key)) {
            return back()->withErrors([
                'email' => "Demasiados intentos fallidos. Reintentá en {$segundos} segundos.",
            ])->withInput($request->only('email'));
        }

        $vendedor = LoginSecurity::findVendedor($request->email);

        if ($vendedor && LoginSecurity::necesitaPasswordInicial($vendedor)) {
            return redirect()->route('cliente.password.inicial', ['tipo' => 'vendedor', 'codigo' => $vendedor->codigo_externo ?? $vendedor->id]);
        }

        if ($vendedor && !$vendedor->activo) {
            LoginSecurity::hit($key);
            return back()->withErrors([
                'email' => LoginSecurity::inactiveMessage(LoginSecurity::TIPO_VENDEDOR, $vendedor),
            ])->withInput($request->only('email'));
        }

        $intentos = [
            ['email'          => $request->email, 'password' => $request->password, 'activo' => true],
            ['codigo_externo' => $request->email, 'password' => $request->password, 'activo' => true],
        ];

        foreach ($intentos as $credentials) {
            if (Auth::guard('vendedor')->attempt($credentials, $request->filled('remember'))) {
                LoginSecurity::clear($key);
                $request->session()->regenerate();
                session(['login_time' => now()->timestamp]);

                $user = Auth::guard('vendedor')->user();

                if (LoginSecurity::necesitaCambioPassword($user)) {
                    session(['debe_cambiar_password' => true]);
                    return redirect()->route('cliente.cambiar-password');
                }

                return redirect()->intended(route('cliente.productos'));
            }
        }

        LoginSecurity::hit($key);

        return back()->withErrors([
            'email' => 'Error en usuario o password.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::guard('vendedor')->logout();
        $request->session()->forget('vendedor_cliente_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
