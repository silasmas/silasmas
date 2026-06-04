<?php

namespace App\Services;

use App\Models\SessionPayment;
use Illuminate\Support\Facades\Http;

/**
 * Vérifie le statut d'un paiement Academy auprès du prestataire.
 */
class AcademyPaymentStatusChecker
{
  /**
   * Interroge l'API check et met à jour le paiement local si confirmé.
   *
   * @param SessionPayment $payment Paiement en base
   * @return array{reponse: bool, status: int, message: string, registration_status?: string}
   */
  public function check(SessionPayment $payment): array
  {
    if ($payment->status === 'paid') {
      return [
        'reponse' => true,
        'status' => 0,
        'message' => 'Paiement déjà confirmé',
        'registration_status' => $payment->registration?->status,
      ];
    }

    $checkKey = $payment->provider_reference ?: $payment->reference;

    if ($checkKey === null || $checkKey === '') {
      return [
        'reponse' => false,
        'status' => 2,
        'message' => 'Paiement en attente d\'initialisation',
      ];
    }

    $checkUrl = rtrim(config('services.flexpay.gateway_check'), '/').'/'.urlencode($checkKey);

    $response = Http::withHeaders([
      'Authorization' => 'Bearer '.config('services.flexpay.token'),
    ])->get($checkUrl);

    if (! $response->successful()) {
      return [
        'reponse' => false,
        'status' => -1,
        'message' => 'Impossible de joindre le service de paiement',
      ];
    }

    $jsonRes = $response->json();
    $transaction = $jsonRes['transaction'] ?? [];
    $status = (int) ($transaction['status'] ?? $jsonRes['status'] ?? -1);

    if (! empty($transaction['orderNumber']) && empty($payment->provider_reference)) {
      $payment->update(['provider_reference' => $transaction['orderNumber']]);
    }

    switch ($status) {
      case 0:
        $this->markPaid($payment);

        return [
          'reponse' => true,
          'status' => 0,
          'message' => 'Paiement effectué avec succès',
          'registration_status' => $payment->registration?->fresh()->status,
        ];

      case 1:
        $payment->update(['status' => 'cancelled']);

        return [
          'reponse' => false,
          'status' => 1,
          'message' => $jsonRes['message'] ?? 'Paiement annulé',
        ];

      case 2:
        return [
          'reponse' => true,
          'status' => 2,
          'message' => 'Paiement en attente de validation sur votre téléphone',
        ];

      default:
        return [
          'reponse' => false,
          'status' => $status,
          'message' => $jsonRes['message'] ?? 'Statut inconnu',
        ];
    }
  }

  /**
   * Marque le paiement comme payé et confirme l'inscription.
   *
   * @param SessionPayment $payment Paiement à finaliser
   * @return void
   */
  protected function markPaid(SessionPayment $payment): void
  {
    $payment->update([
      'status' => 'paid',
      'paid_at' => now(),
    ]);

    if ($payment->registration !== null) {
      $payment->registration->update(['status' => 'confirmed']);
    }
  }
}
