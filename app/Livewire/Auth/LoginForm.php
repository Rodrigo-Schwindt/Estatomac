<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LoginForm extends Component
{
    public $username;
    public $password;

    protected $rules = [
        'username' => 'required',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        $credentials = filter_var($this->username, FILTER_VALIDATE_EMAIL)
            ? ['email' => $this->username, 'password' => $this->password]
            : ['username' => $this->username, 'password' => $this->password];

        if (Auth::attempt($credentials)) {
            session()->regenerate();
            return redirect()->intended('/');
        }

        $this->addError('username', 'Las credenciales no coinciden.');
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}