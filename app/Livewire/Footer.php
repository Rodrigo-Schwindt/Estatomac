<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Subscriber;

class Footer extends Component
{
    public $newsletterEmail;
    public $hasSocialMedia = false;
    public $contactData;

    public function mount()
    {
        $this->contactData = \App\Models\Contact::first();
        $this->hasSocialMedia = $this->contactData &&
            ($this->contactData->insta || 
             $this->contactData->facebook ||
             $this->contactData->linkedin ||
             $this->contactData->youtube);
    }

    public function subscribe()
    {
        $this->validate([
            'newsletterEmail' => 'required|email|unique:subscribers,email'
        ]);

         Subscriber::create([
            'email' => $this->newsletterEmail,
             'active' => true,
         ]);

        $this->reset('newsletterEmail');

        $this->dispatch('toast', message: 'Te suscribiste correctamente!', type: 'success');
    }

    public function render()
    {
        return view('livewire.footer');
    }
    
}
