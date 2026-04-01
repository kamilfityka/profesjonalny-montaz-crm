<?php

namespace App\Mail;

use App\Models\Configuration;
use App\Models\Word;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Contact extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * Create a new message instance.
     *
     */
    public function __construct()
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(Configuration::getValue('site_name') . " - ". Word::getValue('Kontakt'))->view('emails.contact');
    }
}
