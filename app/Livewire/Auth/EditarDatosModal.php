<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditarDatosModal extends Component
{
    public $open = false;

    public $nombre;
    public $email;
    public $cuil;
    public $cuit;
    public $telefono;
    public $domicilio;
    public $localidad;
    public $provincia;
    public $password_actual;
    public $nueva_password;
    public $nueva_password_confirmation;

    protected $listeners = ['open-editar-datos' => 'openModal'];

    public function openModal()
    {
        $cliente = Auth::guard('cliente')->user();
        
        $this->nombre = $cliente->nombre;
        $this->email = $cliente->email;
        $this->cuil = $cliente->cuil;
        $this->cuit = $cliente->cuit;
        $this->telefono = $cliente->telefono;
        $this->domicilio = $cliente->domicilio;
        $this->localidad = $cliente->localidad;
        $this->provincia = $cliente->provincia;
        
        $this->open = true;
    }

    public function actualizar()
    {
        $cliente = Auth::guard('cliente')->user();

        $rules = [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,' . $cliente->id,
            'cuil' => 'nullable|string|max:20',
            'cuit' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:50',
            'domicilio' => 'nullable|string|max:255',
            'localidad' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:100',
        ];

        // Solo validar contraseña si se está intentando cambiar
        if ($this->nueva_password) {
            $rules['password_actual'] = 'required';
            $rules['nueva_password'] = 'required|min:6|confirmed';
        }

        $this->validate($rules);

        // Si se intenta cambiar la contraseña, verificar la actual
        if ($this->nueva_password) {
            if (!Hash::check($this->password_actual, $cliente->password)) {
                $this->addError('password_actual', 'La contraseña actual es incorrecta');
                return;
            }
        }

        try {
            $data = [
                'nombre' => $this->nombre,
                'email' => $this->email,
                'cuil' => $this->cuil,
                'cuit' => $this->cuit,
                'telefono' => $this->telefono,
                'domicilio' => $this->domicilio,
                'localidad' => $this->localidad,
                'provincia' => $this->provincia,
            ];

            // Actualizar contraseña si se proporcionó
            if ($this->nueva_password) {
                $data['password'] = Hash::make($this->nueva_password);
            }

            $cliente->update($data);

            $this->reset([
                'password_actual',
                'nueva_password',
                'nueva_password_confirmation'
            ]);

            $this->open = false;

            $this->dispatch('show-toast', 
                message: 'Tus datos han sido actualizados correctamente',
                type: 'success'
            );

        } catch (\Exception $e) {
            $this->dispatch('show-toast', 
                message: 'Hubo un error al actualizar tus datos. Por favor, intenta nuevamente.',
                type: 'error'
            );
        }
    }

    public function render()
    {
        return view('livewire.auth.editar-datos-modal');
    }
}