<?php

namespace App\Livewire\Vistas\Contact;

use Livewire\Component;
use App\Models\Contact;
use App\Models\Banner;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class ContactPage extends Component
{
    public $contact;
    public $banner;

    public $name;
    public $company;
    public $email;
    public $phone;
    public $message;

    public $successMessage = null;
    public $errorMessage = null;

    protected $rules = [
        'name'    => 'required|string|max:255',
        'company' => 'required|string|max:255',
        'email'   => 'required|email|max:255',
        'phone'   => 'required|string|max:50',
        'message' => 'required|string|min:5',
    ];

    public function mount()
    {
        $this->contact = Contact::first();
        $this->banner = Banner::where('section', 'contacto')->first();
    }

    public function submit()
    {
        $this->validate();

        try {
            Mail::raw(
                "Nuevo mensaje desde el formulario de contacto:\n\n" .
                "Nombre: {$this->name}\n" .
                "Empresa: {$this->company}\n" .
                "Email: {$this->email}\n" .
                "Teléfono: {$this->phone}\n\n" .
                "Mensaje:\n{$this->message}",
                function ($m) {
                    $m->to($this->contact->mail_adm ?? 'info@tudominio.com');
                    $m->subject('Nuevo mensaje desde el sitio web');
                }
            );

            $this->reset(['name', 'company', 'email', 'phone', 'message']);
            $this->successMessage = 'Tu mensaje fue enviado correctamente.';
            $this->errorMessage = null;

        } catch (\Exception $e) {
            $this->errorMessage = 'Ocurrió un error al enviar el mensaje. Intentalo nuevamente.';
            $this->successMessage = null;
        }
    }

    public function render()
    {
        return view('livewire.vistas.contact.contact-page');
    }
}