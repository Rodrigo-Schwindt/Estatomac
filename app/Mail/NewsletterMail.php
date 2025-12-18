<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectText;
    public $bodyText;
    public $attachments;

    /**
     * Create a new message instance.
     */
    public function __construct($subjectText, $bodyText, $attachments = [])
    {
        $this->subjectText = $subjectText;
        $this->bodyText = $bodyText;
        $this->attachments = $attachments;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $email = $this->subject($this->subjectText)
                    ->view('emails.newsletter')
                    ->with([
                        'bodyText' => $this->bodyText,
                    ]);

        // Adjuntar archivos
        foreach ($this->attachments as $attachment) {
            $email->attach($attachment['path'], [
                'as' => $attachment['name'],
                'mime' => $attachment['mime'],
            ]);
        }

        return $email;
    }
}