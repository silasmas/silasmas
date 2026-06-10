<?php

namespace App\Http\Controllers;

use App\Models\SessionPayment;
use App\Services\AcademyPaymentFailureNotifier;
use App\Services\AcademyRegistrationNotifier;
use App\Support\ParticipantToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Webhook FlexPay pour les paiements Academy (Mobile Money / carte).
 */
class AcademyPaymentCallbackController extends Controller
{
  /**
   * Traite la notification serveur FlexPay.
   *
   * @param Request $request Payload JSON FlexPay
   * @return JsonResponse Accusé de réception
   */
  public function handle(Request $request): JsonResponse
  {
    $reference = $request->input('reference')
      ?? $request->input('orderNumber')
      ?? $request->input('transaction.reference');

    if (empty($reference)) {
      return response()->json(['success' => false, 'message' => 'Référence manquante'], 422);
    }

    $payment = SessionPayment::where('reference', $reference)
      ->orWhere('provider_reference', $reference)
      ->with(['registration.student', 'registration.trainingSession'])
      ->first();

    if ($payment === null) {
      return response()->json(['success' => false, 'message' => 'Paiement introuvable'], 404);
    }

    if ($payment->status === 'paid') {
      return response()->json(['success' => true, 'message' => 'Déjà confirmé']);
    }

    $status = $request->input('status')
      ?? $request->input('code')
      ?? $request->input('transaction.status');

    if (is_string($status) && is_numeric($status)) {
      $status = (int) $status;
    }

    $message = $request->input('message')
      ?? $request->input('transaction.message')
      ?? 'Notification FlexPay';

    if ($status === 0 || $status === '0' || $status === 'success') {
      $this->markPaymentPaid($payment);

      return response()->json(['success' => true, 'message' => 'Paiement confirmé']);
    }

    if ($status === 1 || $status === '1' || $status === 'cancel') {
      app(AcademyPaymentFailureNotifier::class)->recordFailure(
        $payment,
        (string) $message,
        'webhook_failed',
        'cancelled',
        [
          'source' => 'flexpay_webhook',
          'payload' => $request->all(),
        ]
      );

      return response()->json(['success' => true, 'message' => 'Échec enregistré']);
    }

    app(AcademyPaymentFailureNotifier::class)->recordFailure(
      $payment,
      (string) $message,
      'webhook_failed',
      'failed',
      [
        'source' => 'flexpay_webhook',
        'payload' => $request->all(),
      ]
    );

    return response()->json(['success' => true, 'message' => 'Échec enregistré']);
  }

  /**
   * Marque un paiement comme payé et confirme l'inscription.
   *
   * @param SessionPayment $payment Paiement à valider
   * @return void
   */
  protected function markPaymentPaid(SessionPayment $payment): void
  {
    $payment->update([
      'status' => 'paid',
      'paid_at' => now(),
    ]);

    $registration = $payment->registration;

    if ($registration === null || $registration->status === 'confirmed') {
      return;
    }

    if (empty($registration->access_token)) {
      $registration->access_token = ParticipantToken::generate();
    }

    $registration->update([
      'status' => 'confirmed',
      'access_token' => $registration->access_token,
    ]);

    $registration->load(['student', 'trainingSession']);
    app(AcademyRegistrationNotifier::class)->sendConfirmation($registration, true);
  }
}
