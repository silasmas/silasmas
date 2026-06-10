<?php

namespace App\Mail;

use App\Models\SessionPayment;
use App\Support\PaymentFailurePresenter;
use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Alerte administrateur lorsqu'un paiement d'inscription Academy échoue.
 */
class AcademyPaymentFailedAdminMail extends Mailable
{
  use Queueable;
  use SerializesModels;

  /**
   * @param SessionPayment $payment Paiement en échec
   */
  public function __construct(public SessionPayment $payment)
  {
  }

  /**
   * Enveloppe du message.
   */
  public function envelope(): Envelope
  {
    $reference = $this->payment->reference ?? 'N/A';

    return new Envelope(
      subject: "Échec paiement Academy — {$reference}",
    );
  }

  /**
   * Contenu Markdown.
   */
  public function content(): Content
  {
    $student = $this->payment->registration?->student ?? $this->payment->student;
    $session = $this->payment->trainingSession;
    $adminPaymentUrl = url(
      Filament::getPanel('admin')->getUrl()
      .'/session-payments/'.$this->payment->id.'/edit'
    );

    $presenter = new PaymentFailurePresenter($this->payment);

    return new Content(
      markdown: 'emails.academy.payment-failed-admin',
      with: [
        'payment' => $this->payment,
        'student' => $student,
        'session' => $session,
        'contextLabel' => SessionPayment::failureContextLabel($this->payment->failure_context),
        'adminPaymentUrl' => $adminPaymentUrl,
        'presenter' => $presenter,
        'serverLines' => $presenter->serverResponseLines(),
      ],
    );
  }
}
