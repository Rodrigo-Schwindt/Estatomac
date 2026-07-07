<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class B2BPasswordResetMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public string $resetUrl;
    public string $nombre;

    public function __construct(string $resetUrl, string $nombre)
    {
        $this->resetUrl = $resetUrl;
        $this->nombre   = $nombre;
    }

    public function build(): self
    {
        return $this->subject('Restablecer contraseña - Zona Privada Todotex')
            ->view('emails.b2b-password-reset');
    }
}
