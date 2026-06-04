<?php

namespace App\Mail;

use App\Models\Registration;
use App\Support\ParticipantToken;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail de confirmation d'inscription / paiement Academy.
 */
class AcademyRegistrationConfirmedMail extends Mailable
{
  use Queueable;
  use SerializesModels;

  /**
   * @param Registration $registration Inscription confirmée
   * @param bool $paymentConfirmed Indique si le message suit un paiement
   */
  public function __construct(
    public Registration $registration,
    public bool $paymentConfirmed = false
  ) {
  }

  /**
   * Enveloppe du message.
   */
  public function envelope(): Envelope
  {
    $title = $this->registration->trainingSession->title ?? 'SDev Academy';

    return new Envelope(
      subject: $this->paymentConfirmed
        ? "Paiement confirmé — {$title}"
        : "Inscription confirmée — {$title}",
    );
  }

  /**
   * Contenu Markdown.
   */
  public function content(): Content
  {
    return new Content(
      markdown: 'emails.academy.registration-confirmed',
      with: [
        'registration' => $this->registration,
        'student' => $this->registration->student,
        'session' => $this->registration->trainingSession,
        'participantUrl' => ParticipantToken::frontendUrl($this->registration),
        'paymentConfirmed' => $this->paymentConfirmed,
      ],
    );
  }
}
