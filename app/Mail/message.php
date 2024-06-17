<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class message extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $objet = "";
    public $nom = "";
    public $phone = "";
    public $message = "";
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $nom, $objet, $msg, $phone)
    {
        $this->email = $email;
        $this->objet = $objet;
        $this->nom = $nom;
        $this->message = $msg;
        $this->phone = $phone;
    }
    public function build()
    {
        return $this->markdown('emails.msg')->subject("Nouveau message sur le site");
    }
}
