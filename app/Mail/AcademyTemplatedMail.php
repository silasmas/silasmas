<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail Academy dont le sujet et le corps proviennent d'un modèle personnalisable.
 */
class AcademyTemplatedMail extends Mailable
{
  use Queueable;
  use SerializesModels;

  /**
   * @param string $mailSubject Sujet déjà rendu
   * @param string $mailBody Corps déjà rendu (texte ou HTML léger)
   * @param string $firstname Prénom du destinataire
   */
  public function __construct(
    public string $mailSubject,
    public string $mailBody,
    public string $firstname = ''
  ) {
  }

  /**
   * Enveloppe du message.
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: $this->mailSubject,
    );
  }

  /**
   * Contenu Markdown.
   */
  public function content(): Content
  {
    return new Content(
      markdown: 'emails.academy.templated',
      with: [
        'mailBody' => $this->mailBody,
        'firstname' => $this->firstname,
      ],
    );
  }
}
