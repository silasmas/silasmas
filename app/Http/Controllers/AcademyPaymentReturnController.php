<?php

namespace App\Http\Controllers;

use App\Models\SessionPayment;
use App\Services\AcademyRegistrationNotifier;
use App\Support\FrontendUrl;
use App\Support\ParticipantToken;
use Illuminate\Http\RedirectResponse;

/**
 * Retours navigateur après paiement carte FlexPay (redirection vers le front Next.js).
 */
class AcademyPaymentReturnController extends Controller
{
  /**
   * Redirige vers la page session du front avec le statut de paiement.
   *
   * @param string $reference Référence ACAD-*
   * @param float $amount Montant
   * @param string $currency Devise
   * @param string $status success|cancel|decline
   */
  public function handle(
    string $reference,
    float $amount,
    string $currency,
    string $status
  ): RedirectResponse {
    $payment = SessionPayment::where('reference', $reference)
      ->with('registration.trainingSession')
      ->first();

    $frontendBase = FrontendUrl::base();
    $slug = $payment?->registration?->trainingSession?->slug ?? '';

    if ($payment === null) {
      return redirect($frontendBase.'/academy?payment=error');
    }

    switch ($status) {
      case 'success':
        $payment->update([
          'status' => 'paid',
          'paid_at' => now(),
          'channel' => 'card',
          'payment_method' => 'card',
        ]);

        $registration = $payment->registration;

        if ($registration !== null) {
          $registration->update(['status' => 'confirmed']);

          if (empty($registration->access_token)) {
            $registration->update(['access_token' => ParticipantToken::generate()]);
            $registration->refresh();
          }

          app(AcademyRegistrationNotifier::class)->sendConfirmation($registration, true);
        }

        $token = $registration?->access_token;
        $target = $token
          ? "{$frontendBase}/academy/espace/{$token}?payment=success"
          : "{$frontendBase}/academy/{$slug}?payment=success";

        return redirect($target);

      case 'cancel':
      case 'decline':
        $payment->update(['status' => 'cancelled']);

        $query = http_build_query([
          'payment' => $status,
          'reference' => $reference,
        ]);

        return redirect("{$frontendBase}/academy/{$slug}?{$query}");

      default:
        return redirect($frontendBase.'/academy?payment=error');
    }
  }
}
