<?php

namespace App\Livewire\Auth;

use App\Models\Cliente;
use App\Services\LoginSecurity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class LoginModal extends Component
{
    public $open = false;
    public $isLogin = true;

    // Login
    public $username = '';
    public $password = '';

    // Register
    public $nombre;
    public $email;
    public $cuil;
    public $cuit;
    public $telefono;
    public $domicilio;
    public $localidad;
    public $provincia;
    public $reg_password;
    public $reg_password_confirmation;

    protected $listeners = ['open-login' => 'openModal'];

    public function openModal()
    {
        $this->open = true;
    }

    public function login()
    {
        $this->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $key = LoginSecurity::rateKey('any', $this->username, request());

        if ($segundos = LoginSecurity::isThrottled($key)) {
            $this->addError('username', "Demasiados intentos fallidos. Reintentá en {$segundos} segundos.");
            return;
        }

        $cliente  = LoginSecurity::findCliente($this->username);
        $vendedor = $cliente ? null : LoginSecurity::findVendedor($this->username);
        $usuario  = $cliente ?? $vendedor;

        if ($usuario && LoginSecurity::necesitaPasswordInicial($usuario)) {
            $tipo   = $cliente ? 'cliente' : 'vendedor';
            $codigo = $cliente
                ? ($cliente->codigo ?? $cliente->id)
                : ($vendedor->codigo_externo ?? $vendedor->id);
            $this->open = false;
            return redirect()->route('cliente.password.inicial', ['tipo' => $tipo, 'codigo' => $codigo]);
        }

        if ($usuario && !$usuario->activo) {
            LoginSecurity::hit($key);
            $tipo = $cliente ? LoginSecurity::TIPO_CLIENTE : LoginSecurity::TIPO_VENDEDOR;
            $this->addError('username', LoginSecurity::inactiveMessage($tipo, $usuario));
            return;
        }

        $intentos = [
            ['guard' => 'cliente',  'field' => 'email'],
            ['guard' => 'cliente',  'field' => 'usuario'],
            ['guard' => 'cliente',  'field' => 'codigo'],
            ['guard' => 'vendedor', 'field' => 'email'],
            ['guard' => 'vendedor', 'field' => 'codigo_externo'],
        ];

        foreach ($intentos as $intento) {
            if (Auth::guard($intento['guard'])->attempt([
                $intento['field'] => $this->username,
                'password'        => $this->password,
                'activo'          => true,
            ])) {
                LoginSecurity::clear($key);
                $user = Auth::guard($intento['guard'])->user();
                return $this->redirectSuccess($user);
            }
        }

        LoginSecurity::hit($key);
        $this->addError('username', 'Error en usuario o password.');
    }

    private function redirectSuccess($user)
    {
        if (LoginSecurity::necesitaCambioPassword($user)) {
            session(['debe_cambiar_password' => true]);
            session()->regenerate();
            $this->open = false;
            return redirect()->route('cliente.cambiar-password');
        }

        session()->regenerate();
        session(['login_time' => now()->timestamp]);
        $this->reset(['username', 'password']);
        $this->open = false;

        $this->dispatch('show-toast', message: 'Sesión iniciada correctamente', type: 'success');
        return redirect()->route('cliente.productos');
    }

    public function register()
    {
        $this->validate([
            'nombre'                  => 'required|string|max:255',
            'email'                   => 'required|email|unique:clientes,email',
            'cuil'                    => 'nullable|string|max:20',
            'cuit'                    => 'nullable|string|max:20',
            'telefono'                => 'nullable|string|max:50',
            'domicilio'               => 'nullable|string|max:255',
            'localidad'               => 'nullable|string|max:100',
            'provincia'               => 'nullable|string|max:100',
            'reg_password'            => 'required|min:6|confirmed',
        ]);

        try {
            $cliente = Cliente::create([
                'nombre'    => $this->nombre,
                'email'     => $this->email,
                'usuario'   => $this->email,
                'cuil'      => $this->cuil,
                'cuit'      => $this->cuit,
                'telefono'  => $this->telefono,
                'domicilio' => $this->domicilio,
                'localidad' => $this->localidad,
                'provincia' => $this->provincia,
                'password'  => Hash::make($this->reg_password),
                'activo'    => false,
            ]);

            $contactData = \App\Models\Contact::first();
            if ($contactData && $contactData->mail_adm) {
                $mailData = [
                    'nombre'          => $this->nombre,
                    'email'           => $this->email,
                    'usuario'         => $this->email,
                    'cuil'            => $this->cuil,
                    'cuit'            => $this->cuit,
                    'telefono'        => $this->telefono,
                    'domicilio'       => $this->domicilio,
                    'localidad'       => $this->localidad,
                    'provincia'       => $this->provincia,
                    'fecha_registro'  => now()->format('d/m/Y H:i'),
                ];

                \Illuminate\Support\Facades\Mail::to($contactData->mail_adm)
                    ->send(new \App\Mail\NuevoRegistroMail($mailData));
            }

            $this->reset([
                'nombre', 'email', 'cuil', 'cuit', 'telefono',
                'domicilio', 'localidad', 'provincia',
                'reg_password', 'reg_password_confirmation',
            ]);

            $this->open    = false;
            $this->isLogin = true;

            $this->dispatch('show-toast',
                message: 'Tu cuenta está a la espera de aprobación. Se te notificará por email cuando esté aprobada.',
                type: 'info'
            );
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                message: 'Hubo un error al crear tu cuenta. Por favor, intenta nuevamente.',
                type: 'error'
            );
        }
    }

    public function render()
    {
        return view('livewire.auth.login-modal');
    }
}
