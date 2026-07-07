<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LoginSecurity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('cliente')->check()) {
            return redirect()->route('cliente.productos');
        }

        return redirect()->route('home')->with('openLoginModal', true);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $key = LoginSecurity::rateKey(LoginSecurity::TIPO_CLIENTE, $request->username, $request);

        if ($segundos = LoginSecurity::isThrottled($key)) {
            return back()->withErrors([
                'username' => "Demasiados intentos fallidos. Reintentá en {$segundos} segundos.",
            ])->withInput($request->only('username'));
        }

        $cliente = LoginSecurity::findCliente($request->username);

        if ($cliente && LoginSecurity::necesitaPasswordInicial($cliente)) {
            return redirect()->route('cliente.password.inicial', ['tipo' => 'cliente', 'codigo' => $cliente->codigo ?? $cliente->id]);
        }

        if ($cliente && !$cliente->activo) {
            LoginSecurity::hit($key);
            return back()->withErrors([
                'username' => LoginSecurity::inactiveMessage(LoginSecurity::TIPO_CLIENTE, $cliente),
            ])->withInput($request->only('username'));
        }

        $intentos = [
            ['email'   => $request->username, 'password' => $request->password, 'activo' => true],
            ['usuario' => $request->username, 'password' => $request->password, 'activo' => true],
            ['codigo'  => $request->username, 'password' => $request->password, 'activo' => true],
        ];

        foreach ($intentos as $credentials) {
            if (Auth::guard('cliente')->attempt($credentials, $request->filled('remember'))) {
                LoginSecurity::clear($key);
                $request->session()->regenerate();
                session(['login_time' => now()->timestamp]);

                $user = Auth::guard('cliente')->user();

                if (LoginSecurity::necesitaCambioPassword($user)) {
                    session(['debe_cambiar_password' => true]);
                    return redirect()->route('cliente.cambiar-password');
                }

                return redirect()->intended(route('cliente.productos'));
            }
        }

        LoginSecurity::hit($key);

        return back()->withErrors([
            'username' => 'Error en usuario o password.',
        ])->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        Auth::guard('cliente')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Sesión cerrada correctamente');
    }
}
