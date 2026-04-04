<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReclamationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected string $emailSubject,
        protected string $emailBody
    ) {}

    public function build()
    {
        return $this->subject($this->emailSubject)
            ->view('emails.reclamation', [
                'emailBody' => $this->emailBody,
            ]);
    }
}
