<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('cliente')->check()) {
            return redirect()->route('cliente.productos');
        }
        
        // Redirigir al home con el modal abierto
        return redirect()->route('home')->with('openLoginModal', true);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Intentar login con email
        $emailCredentials = [
            'email' => $request->username,
            'password' => $request->password,
            'activo' => true,
        ];

        if (Auth::guard('cliente')->attempt($emailCredentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('cliente.productos'));
        }

        // Si falla, intentar con usuario
        $usuarioCredentials = [
            'usuario' => $request->username,
            'password' => $request->password,
            'activo' => true,
        ];

        if (Auth::guard('cliente')->attempt($usuarioCredentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('cliente.productos'));
        }

        return back()->withErrors([
            'username' => 'Las credenciales no coinciden con nuestros registros o el usuario está deshabilitado.',
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