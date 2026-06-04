<?php

namespace App\Services;

use App\Models\SessionPayment;

/**
 * Vérification du statut de transaction auprès de l'API de paiement.
 */
class FlexPayCheckService
{
  /**
   * Interroge l'API check pour une référence donnée.
   *
   * @param string $reference Référence transaction
   * @return array{ok: bool, status: int, body: array<string, mixed>|null, error?: string}
   */
  public function fetchStatus(string $reference): array
  {
    $checkUrl = rtrim(config('services.flexpay.gateway_check'), '/').'/'.urlencode($reference);

    $curl = curl_init($checkUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
      'Authorization: Bearer '.config('services.flexpay.token'),
      'Accept: application/json',
    ]);
    $curlResponse = curl_exec($curl);
    $curlError = curl_errno($curl);
    curl_close($curl);

    if ($curlError) {
      return ['ok' => false, 'status' => -1, 'body' => null, 'error' => 'network'];
    }

    $jsonRes = json_decode($curlResponse, true);

    if (! is_array($jsonRes)) {
      return ['ok' => false, 'status' => -1, 'body' => null, 'error' => 'invalid_json'];
    }

    $status = $jsonRes['transaction']['status'] ?? $jsonRes['status'] ?? -1;

    if (is_string($status) && is_numeric($status)) {
      $status = (int) $status;
    }

    return [
      'ok' => true,
      'status' => (int) $status,
      'body' => $jsonRes,
    ];
  }

  /**
   * Résout le statut d'un paiement (plusieurs références possibles).
   *
   * @param SessionPayment $payment Paiement local
   * @return array{paid: bool, pending: bool, cancelled: bool, status: int, message: string, checked_reference?: string}
   */
  public function resolvePaymentStatus(SessionPayment $payment): array
  {
    if ($payment->status === 'paid') {
      return [
        'paid' => true,
        'pending' => false,
        'cancelled' => false,
        'status' => 0,
        'message' => 'Paiement déjà confirmé',
      ];
    }

    $references = array_values(array_unique(array_filter([
      $payment->reference,
      $payment->provider_reference,
    ])));

    $lastStatus = -1;
    $lastMessage = 'Statut en attente';

    foreach ($references as $ref) {
      $result = $this->fetchStatus($ref);

      if (! $result['ok']) {
        continue;
      }

      $status = $result['status'];
      $lastStatus = $status;
      $body = $result['body'] ?? [];
      $lastMessage = $body['message'] ?? $body['transaction']['message'] ?? $lastMessage;

      if ($status === 0) {
        return [
          'paid' => true,
          'pending' => false,
          'cancelled' => false,
          'status' => 0,
          'message' => 'Paiement effectué avec succès',
          'checked_reference' => $ref,
        ];
      }

      if ($status === 1) {
        return [
          'paid' => false,
          'pending' => false,
          'cancelled' => true,
          'status' => 1,
          'message' => $lastMessage ?: 'Paiement annulé',
          'checked_reference' => $ref,
        ];
      }

      if ($status === 2) {
        return [
          'paid' => false,
          'pending' => true,
          'cancelled' => false,
          'status' => 2,
          'message' => 'En attente de validation sur votre téléphone',
          'checked_reference' => $ref,
        ];
      }
    }

    return [
      'paid' => false,
      'pending' => $lastStatus === 2,
      'cancelled' => $lastStatus === 1,
      'status' => $lastStatus,
      'message' => $lastMessage,
    ];
  }
}
