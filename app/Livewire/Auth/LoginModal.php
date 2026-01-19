<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LoginModal extends Component
{
    public $open = false;
    public $username = '';
    public $password = '';
    
    protected $listeners = ['open-login' => 'openModal'];

    public function openModal()
    {
        $this->open = true;
    }

    protected $rules = [
        'username' => 'required|string',
        'password' => 'required|string',
    ];

    protected $messages = [
        'username.required' => 'El usuario o email es requerido',
        'password.required' => 'La contraseña es requerida',
    ];

    public function login()
    {
        $this->validate();

        // Intentar login con email
        $emailCredentials = [
            'email' => $this->username,
            'password' => $this->password,
            'activo' => true,
        ];

        if (Auth::guard('cliente')->attempt($emailCredentials)) {
            session()->regenerate();
            $this->reset(['username', 'password']);
            $this->open = false;

            $this->dispatch('show-toast', message: 'Sesión iniciada correctamente', type: 'success');
            return redirect()->route('cliente.productos');
        }

        // Si falla, intentar con usuario
        $usuarioCredentials = [
            'usuario' => $this->username,
            'password' => $this->password,
            'activo' => true,
        ];

        if (Auth::guard('cliente')->attempt($usuarioCredentials)) {
            session()->regenerate();
            $this->reset(['username', 'password']);
            $this->open = false;

            $this->dispatch('show-toast', message: 'Sesión iniciada correctamente', type: 'success');
            return redirect()->route('cliente.productos');
        }

        $this->addError('username', 'Credenciales inválidas o cuenta desactivada');
    }

    public function render()
    {
        return view('livewire.auth.login-modal');
    }
}