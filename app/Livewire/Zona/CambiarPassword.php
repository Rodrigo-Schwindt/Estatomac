<?php

namespace App\Livewire\Zona;

use App\Models\Parametro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.zone')]
class CambiarPassword extends Component
{
    public string $password_actual      = '';
    public string $password_nueva       = '';
    public string $password_confirmar   = '';
    public bool   $forzado              = false;

    public function mount(): void
    {
        if (!Auth::guard('cliente')->check() && !Auth::guard('vendedor')->check()) {
            $this->redirect(route('home'));
            return;
        }

        $this->forzado = (bool) session('debe_cambiar_password');
    }

    public function cambiar(): void
    {
        $parametro   = Parametro::config();
        $largoMinimo = max(6, $parametro->largo_passwd);

        $this->validate([
            'password_actual'  => 'required|string',
            'password_nueva'   => "required|string|min:{$largoMinimo}",
            'password_confirmar' => 'required|string|same:password_nueva',
        ], [
            'password_nueva.min'         => "La nueva contraseña debe tener al menos {$largoMinimo} caracteres.",
            'password_confirmar.same'    => 'La confirmación no coincide con la nueva contraseña.',
        ]);

        $user = Auth::guard('vendedor')->check()
            ? Auth::guard('vendedor')->user()
            : Auth::guard('cliente')->user();

        if (!Hash::check($this->password_actual, $user->password)) {
            $this->addError('password_actual', 'La contraseña actual es incorrecta.');
            return;
        }

        $user->password            = $this->password_nueva;
        $user->password_changed_at = now();
        $user->save();

        session()->forget('debe_cambiar_password');

        $this->dispatch('show-toast', message: 'Contraseña actualizada correctamente.', type: 'success');
        $this->redirect(route('cliente.productos'));
    }

    public function render()
    {
        return view('livewire.zona.cambiar-password');
    }
}
