<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail informant qu'une pré-inscrite que les inscriptions sont ouvertes.
 */
class AcademyRegistrationOpenMail extends Mailable
{
  use Queueable;
  use SerializesModels;

  /**
   * @param Registration $registration Pré-inscription concernée
   * @param string $registrationUrl Lien vers le formulaire d'inscription
   */
  public function __construct(
    public Registration $registration,
    public string $registrationUrl
  ) {
  }

  /**
   * Enveloppe du message.
   */
  public function envelope(): Envelope
  {
    $title = $this->registration->trainingSession->title ?? 'SDev Academy';

    return new Envelope(
      subject: "Inscriptions ouvertes — {$title}",
    );
  }

  /**
   * Contenu Markdown.
   */
  public function content(): Content
  {
    return new Content(
      markdown: 'emails.academy.registration-open',
      with: [
        'registration' => $this->registration,
        'student' => $this->registration->student,
        'session' => $this->registration->trainingSession,
        'registrationUrl' => $this->registrationUrl,
      ],
    );
  }
}
