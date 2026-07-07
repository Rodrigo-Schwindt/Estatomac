<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\B2BPasswordResetMail;
use App\Models\Cliente;
use App\Models\Parametro;
use App\Models\Vendedor;
use App\Services\LoginSecurity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class B2BPasswordController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.password-forgot');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'tipo'  => 'required|in:cliente,vendedor',
            'email' => 'required|email',
        ]);

        $user = $this->findByEmail($request->tipo, $request->email);

        if (!$user || empty($user->email)) {
            $soporte = Parametro::config()->email_soporte;
            return back()->withErrors([
                'email' => 'Dirección de correo electrónico no informada – Diríjase a Soporte de Ventas' . ($soporte ? " ({$soporte})" : '.'),
            ])->withInput();
        }

        $token = Str::random(64);

        DB::table('b2b_password_resets')->updateOrInsert(
            ['tipo' => $request->tipo, 'email' => $user->email],
            ['token' => hash('sha256', $token), 'created_at' => now()]
        );

        $url = route('cliente.password.reset.form', [
            'tipo'  => $request->tipo,
            'token' => $token,
            'email' => $user->email,
        ]);

        try {
            Mail::to($user->email)->send(new B2BPasswordResetMail($url, $user->nombre ?? $user->email));
        } catch (\Throwable $e) {
            return back()->withErrors([
                'email' => 'No pudimos enviar el correo. Por favor, intentá nuevamente más tarde.',
            ]);
        }

        return back()->with('success', 'Si la cuenta existe, te enviamos un correo con instrucciones para restablecer tu contraseña.');
    }

    public function showResetForm(Request $request)
    {
        return view('auth.password-reset', [
            'tipo'  => $request->query('tipo'),
            'token' => $request->query('token'),
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request)
    {
        $largo = max(6, (int) Parametro::config()->largo_passwd);

        $request->validate([
            'tipo'                  => 'required|in:cliente,vendedor',
            'email'                 => 'required|email',
            'token'                 => 'required|string',
            'password'              => ['required', 'string', "min:{$largo}", 'confirmed'],
        ]);

        $registro = DB::table('b2b_password_resets')
            ->where('tipo', $request->tipo)
            ->where('email', $request->email)
            ->first();

        if (!$registro || !hash_equals($registro->token, hash('sha256', $request->token))) {
            return back()->withErrors(['token' => 'El enlace de restablecimiento es inválido.']);
        }

        if (now()->diffInMinutes($registro->created_at) > 60) {
            return back()->withErrors(['token' => 'El enlace de restablecimiento expiró. Solicitá uno nuevo.']);
        }

        $user = $this->findByEmail($request->tipo, $request->email);
        if (!$user) {
            return back()->withErrors(['email' => 'No encontramos la cuenta.']);
        }

        $user->password            = $request->password;
        $user->password_changed_at = now();
        $user->save();

        DB::table('b2b_password_resets')
            ->where('tipo', $request->tipo)
            ->where('email', $request->email)
            ->delete();

        Auth::guard($request->tipo)->login($user);
        session(['login_time' => now()->timestamp]);
        $request->session()->regenerate();

        return redirect()->route('cliente.productos')->with('success', 'Contraseña actualizada. ¡Bienvenido!');
    }

    public function showInicialForm(Request $request)
    {
        $tipo   = $request->query('tipo');
        $codigo = $request->query('codigo');

        $user = $this->findByCodigo($tipo, $codigo);
        abort_unless($user && empty($user->password), 404);

        return view('auth.password-inicial', [
            'tipo'   => $tipo,
            'codigo' => $codigo,
            'nombre' => $user->nombre ?? '',
        ]);
    }

    public function storeInicial(Request $request)
    {
        $largo = max(6, (int) Parametro::config()->largo_passwd);

        $request->validate([
            'tipo'     => 'required|in:cliente,vendedor',
            'codigo'   => 'required|string',
            'password' => ['required', 'string', "min:{$largo}", 'confirmed'],
        ]);

        $user = $this->findByCodigo($request->tipo, $request->codigo);
        if (!$user || !empty($user->password)) {
            return back()->withErrors(['codigo' => 'No corresponde establecer una contraseña inicial para este operador.']);
        }

        if (!$user->activo) {
            return back()->withErrors([
                'codigo' => LoginSecurity::inactiveMessage($request->tipo, $user),
            ]);
        }

        $user->password            = $request->password;
        $user->password_changed_at = now();
        $user->save();

        Auth::guard($request->tipo)->login($user);
        session(['login_time' => now()->timestamp]);
        $request->session()->regenerate();

        return redirect()->route('cliente.productos')->with('success', 'Contraseña establecida correctamente.');
    }

    private function findByEmail(string $tipo, string $email): ?Model
    {
        return $tipo === 'vendedor'
            ? Vendedor::where('email', $email)->first()
            : Cliente::where('email', $email)->first();
    }

    private function findByCodigo(string $tipo, string $codigo): ?Model
    {
        if ($tipo === 'vendedor') {
            return Vendedor::where('codigo_externo', $codigo)->orWhere('id', $codigo)->first();
        }
        return Cliente::where('codigo', $codigo)->orWhere('id', $codigo)->first();
    }
}
