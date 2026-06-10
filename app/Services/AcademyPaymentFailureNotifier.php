<?php

namespace App\Services;

use App\Mail\AcademyPaymentFailedAdminMail;
use App\Models\SessionPayment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Enregistre les échecs de paiement Academy et alerte l'administrateur par e-mail.
 */
class AcademyPaymentFailureNotifier
{
  /**
   * Enregistre un échec et envoie une alerte admin (une seule fois par incident).
   *
   * @param SessionPayment $payment Paiement concerné
   * @param string $reason Message lisible décrivant l'échec
   * @param string $context Code technique (mobile_init, card_decline, etc.)
   * @param string $status Statut local : failed ou cancelled
   * @return void
   */
  public function recordFailure(
    SessionPayment $payment,
    string $reason,
    string $context,
    string $status = 'failed'
  ): void {
    if ($payment->status === 'paid') {
      return;
    }

    $payment->loadMissing(['registration.student', 'trainingSession', 'student']);

    $normalizedReason = trim($reason) !== '' ? trim($reason) : 'Échec de paiement sans détail';
    $shouldNotify = $payment->admin_notified_at === null
      || $payment->failure_context !== $context;

    $payment->update([
      'status' => $status,
      'failure_context' => $context,
      'failure_reason' => $normalizedReason,
      'failed_at' => now(),
    ]);

    if (! $shouldNotify) {
      return;
    }

    $this->sendAdminAlert($payment->fresh());
    $payment->update(['admin_notified_at' => now()]);
  }

  /**
   * Envoie l'e-mail d'alerte à l'adresse configurée.
   *
   * @param SessionPayment $payment Paiement avec relations chargées
   * @return void
   */
  protected function sendAdminAlert(SessionPayment $payment): void
  {
    $recipient = config('services.academy.admin_alert_email');

    if (empty($recipient)) {
      Log::warning('Academy payment failure alert skipped: no admin email configured', [
        'payment_id' => $payment->id,
        'reference' => $payment->reference,
      ]);

      return;
    }

    try {
      Mail::to($recipient)->send(new AcademyPaymentFailedAdminMail($payment));
    } catch (\Throwable $exception) {
      Log::error('Academy payment failure alert email failed', [
        'payment_id' => $payment->id,
        'reference' => $payment->reference,
        'error' => $exception->getMessage(),
      ]);
    }
  }
}
