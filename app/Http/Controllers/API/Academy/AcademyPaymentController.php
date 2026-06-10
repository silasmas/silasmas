<?php

namespace App\Http\Controllers\API\Academy;

use App\Http\Controllers\API\BaseController;
use App\Models\SessionPayment;
use App\Services\AcademyPaymentFailureNotifier;
use App\Services\AcademyRegistrationNotifier;
use App\Services\FlexPayCheckService;
use App\Services\FlexPayService;
use App\Support\ParticipantToken;
use App\Support\MobileMoneyValidation;
use Illuminate\Http\Request;

/**
 * API paiement pour inscriptions Academy payantes.
 */
class AcademyPaymentController extends BaseController
{
  /**
   * Lance le paiement Mobile Money ou carte pour une inscription.
   */
  public function processPayment(Request $request, FlexPayService $flexPayService)
  {
    $validated = $request->validate([
      'reference' => ['required', 'string', 'exists:session_payments,reference'],
      'channel' => ['required', 'in:mobile_money,card'],
      'phone' => ['required_if:channel,mobile_money', 'nullable', 'string', 'max:30'],
      'mobile_operator' => ['required_if:channel,mobile_money', 'nullable', 'in:mpesa,airtel,orange,afrimoney'],
    ], [
      'phone.required_if' => 'Le numéro de téléphone est obligatoire pour Mobile Money.',
      'mobile_operator.required_if' => 'Choisissez votre opérateur Mobile Money.',
    ]);

    $payment = SessionPayment::where('reference', $validated['reference'])
      ->with(['registration.trainingSession', 'registration.student'])
      ->firstOrFail();

    if ($payment->status === 'paid') {
      return $this->handleResponse([
        'reponse' => true,
        'type' => 'already_paid',
        'status' => 0,
      ], 'Paiement déjà confirmé');
    }

    $registration = $payment->registration;

    if ($registration === null || $registration->status === 'cancelled') {
      return $this->handleError('Inscription introuvable ou annulée.', [], 422);
    }

    $session = $registration->trainingSession;

    if ($session === null) {
      return $this->handleError('Session introuvable.', [], 422);
    }

    $enabledChannels = $session->enabledPaymentChannels();

    if ($enabledChannels === []) {
      return $this->handleError('Aucun moyen de paiement n\'est disponible pour cette session.', [], 422);
    }

    if (! in_array($validated['channel'], $enabledChannels, true)) {
      return $this->handleError('Ce moyen de paiement n\'est pas disponible pour cette session.', [], 422);
    }

    if ($validated['channel'] === 'mobile_money') {
      $operator = $validated['mobile_operator'] ?? 'mpesa';
      $phoneCheck = MobileMoneyValidation::validateForOperator(
        $validated['phone'] ?? '',
        $operator
      );

      if (! $phoneCheck['valid']) {
        return $this->handleError($phoneCheck['message'] ?? 'Numéro invalide.', [], 422);
      }

      $normalizedPhone = $phoneCheck['normalized'];
      $payment->update([
        'payment_method' => 'mobile_money',
        'mobile_operator' => $operator,
      ]);

      $data = [
        'merchant' => config('services.flexpay.merchant'),
        'type' => flexpayMobileTypeForOperator($operator),
        'phone' => $normalizedPhone,
        'reference' => $payment->reference,
        'amount' => (float) $payment->amount,
        'currency' => $payment->currency,
        'callbackUrl' => rtrim(config('app.url'), '/').'/academy/payment/callback',
      ];

      $rep = initRequeteFlexPayMobile($data, $payment);

      if (! ($rep['reponse'] ?? false)) {
        app(AcademyPaymentFailureNotifier::class)->recordFailure(
          $payment,
          $rep['message'] ?? 'Échec du paiement Mobile Money',
          'mobile_init',
          'failed',
          $rep['server_response'] ?? null
        );

        return $this->handleError($rep['message'] ?? 'Échec du paiement Mobile Money', [], 400);
      }

      return $this->handleResponse([
        'reponse' => true,
        'message' => $rep['message'] ?? 'Paiement initié',
        'type' => 'mobile',
        'reference' => $payment->reference,
        'orderNumber' => $rep['orderNumber'] ?? $payment->provider_reference,
      ], $rep['message'] ?? 'Paiement initié');
    }

    $sessionTitle = $registration->trainingSession->title ?? 'SDev Academy';
    $studentName = trim(
      ($registration->student->firstname ?? '').' '.($registration->student->lastname ?? '')
    );

    $retour = $flexPayService->initiatePayment(
      (float) $payment->amount,
      $payment->currency,
      $payment->reference,
      "Inscription — {$sessionTitle} — {$studentName}"
    );

    if ($retour['rep']) {
      $payment->update([
        'provider_reference' => $retour['orderNumber'] ?? null,
        'status' => 'processing',
        'channel' => 'card',
        'payment_method' => 'card',
      ]);

      return $this->handleResponse([
        'reponse' => true,
        'redirect_url' => $retour['url'],
        'type' => 'card',
      ], 'Redirection vers le paiement par carte');
    }

    app(AcademyPaymentFailureNotifier::class)->recordFailure(
      $payment,
      $retour['message'] ?? 'Échec de l\'initiation du paiement',
      'card_init',
      'failed',
      $retour['server_response'] ?? null
    );

    return $this->handleError($retour['message'] ?? 'Échec de l\'initiation du paiement', [], 400);
  }

  /**
   * Vérifie le statut d'une transaction (polling ou relance manuelle).
   */
  public function checkTransactionStatus(Request $request, FlexPayCheckService $checkService)
  {
    $reference = $request->input('reference');

    if (empty($reference)) {
      return $this->handleError('Référence de paiement requise.', [], 422);
    }

    $payment = SessionPayment::where('reference', $reference)
      ->orWhere('provider_reference', $reference)
      ->with('registration')
      ->first();

    if ($payment === null) {
      return $this->handleError('Paiement introuvable', [], 404);
    }

    $resolved = $checkService->resolvePaymentStatus($payment);

    if ($resolved['paid']) {
      $this->markPaymentPaid($payment);

      return $this->handleResponse([
        'reponse' => true,
        'status' => 0,
        'confirmed' => true,
        'message' => $resolved['message'],
        'registration_status' => $payment->registration?->fresh()->status,
      ], 'Paiement confirmé');
    }

    if ($resolved['cancelled']) {
      app(AcademyPaymentFailureNotifier::class)->recordFailure(
        $payment,
        $resolved['message'] ?: 'Paiement annulé',
        'polling_cancelled',
        'cancelled',
        $resolved['server_response'] ?? null
      );

      return $this->handleResponse([
        'reponse' => false,
        'status' => 1,
        'confirmed' => false,
        'message' => $resolved['message'],
      ], 'Paiement annulé', 200);
    }

    if ($resolved['pending']) {
      return $this->handleResponse([
        'reponse' => true,
        'status' => 2,
        'confirmed' => false,
        'message' => $resolved['message'],
        'orderNumber' => $payment->provider_reference,
      ], 'En attente');
    }

    if (
      ! $resolved['pending']
      && ! $resolved['paid']
      && ! $resolved['cancelled']
      && $resolved['status'] !== -1
    ) {
      app(AcademyPaymentFailureNotifier::class)->recordFailure(
        $payment,
        $resolved['message'] ?: 'Paiement non confirmé',
        'polling_failed',
        'failed',
        $resolved['server_response'] ?? null
      );
    }

    return $this->handleResponse([
      'reponse' => false,
      'status' => $resolved['status'],
      'confirmed' => false,
      'message' => $resolved['message'],
    ], 'Statut non confirmé', 200);
  }

  /**
   * Relance explicite de confirmation (après débit sans retour automatique).
   */
  public function confirmPayment(Request $request, FlexPayCheckService $checkService)
  {
    return $this->checkTransactionStatus($request, $checkService);
  }

  /**
   * Marque un paiement comme payé et confirme l'inscription.
   */
  protected function markPaymentPaid(SessionPayment $payment): void
  {
    if ($payment->status === 'paid') {
      return;
    }

    $payment->update([
      'status' => 'paid',
      'paid_at' => now(),
    ]);

    $registration = $payment->registration;

    if ($registration !== null && $registration->status !== 'confirmed') {
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
}
