<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginModal2 extends Component
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

        $credentialsEmail = [
            'email' => $this->username,
            'password' => $this->password,
            'activo' => true,
        ];

        if (Auth::guard('cliente')->attempt($credentialsEmail)) {
            return $this->redirectSuccess();
        }

        $credentialsUser = [
            'usuario' => $this->username,
            'password' => $this->password,
            'activo' => true,
        ];

        if (Auth::guard('cliente')->attempt($credentialsUser)) {
            return $this->redirectSuccess();
        }

        $this->addError('username', 'Credenciales inválidas o cuenta desactivada');
    }

    public function register()
{
    $this->validate([
        'nombre' => 'required|string|max:255',
        'email' => 'required|email|unique:clientes,email',
        'cuil' => 'nullable|string|max:20',
        'cuit' => 'nullable|string|max:20',
        'telefono' => 'nullable|string|max:50',
        'domicilio' => 'nullable|string|max:255',
        'localidad' => 'nullable|string|max:100',
        'provincia' => 'nullable|string|max:100',
        'reg_password' => 'required|min:6|confirmed',
    ]);

    try {
        $cliente = Cliente::create([
            'nombre' => $this->nombre,
            'email' => $this->email,
            'usuario' => $this->email,
            'cuil' => $this->cuil,
            'cuit' => $this->cuit,
            'telefono' => $this->telefono,
            'domicilio' => $this->domicilio,
            'localidad' => $this->localidad,
            'provincia' => $this->provincia,
            'password' => Hash::make($this->reg_password),
            'activo' => false,
        ]);

        $contactData = \App\Models\Contact::first();
        if ($contactData && $contactData->mail_adm) {
            $mailData = [
                'nombre' => $this->nombre,
                'email' => $this->email,
                'usuario' => $this->email,
                'cuil' => $this->cuil,
                'cuit' => $this->cuit,
                'telefono' => $this->telefono,
                'domicilio' => $this->domicilio,
                'localidad' => $this->localidad,
                'provincia' => $this->provincia,
                'fecha_registro' => now()->format('d/m/Y H:i'),
            ];

            \Illuminate\Support\Facades\Mail::to($contactData->mail_adm)
                ->send(new \App\Mail\NuevoRegistroMail($mailData));
        }

        // Resetear campos
        $this->reset([
            'nombre', 
            'email', 
            'cuil', 
            'cuit', 
            'telefono', 
            'domicilio', 
            'localidad', 
            'provincia', 
            'reg_password', 
            'reg_password_confirmation'
        ]);
        
        $this->open = false;
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

    private function redirectSuccess()
    {
        session()->regenerate();
        $this->reset([
            'username', 
            'password'
        ]);
        $this->open = false;
        
        $this->dispatch('show-toast', 
            message: 'Sesión iniciada correctamente',
            type: 'success'
        );
        
        return redirect()->route('cliente.productos');
    }

    public function render()
    {
        return view('livewire.auth.login-modal2');
    }
}