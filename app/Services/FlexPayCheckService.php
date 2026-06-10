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
   * @return array{ok: bool, status: int, body: array<string, mixed>|null, error?: string, raw?: string|null}
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
      return [
        'ok' => false,
        'status' => -1,
        'body' => null,
        'error' => 'network',
        'raw' => is_string($curlResponse) ? $curlResponse : null,
      ];
    }

    $jsonRes = json_decode($curlResponse, true);

    if (! is_array($jsonRes)) {
      return [
        'ok' => false,
        'status' => -1,
        'body' => null,
        'error' => 'invalid_json',
        'raw' => is_string($curlResponse) ? $curlResponse : null,
      ];
    }

    $status = $jsonRes['transaction']['status'] ?? $jsonRes['status'] ?? -1;

    if (is_string($status) && is_numeric($status)) {
      $status = (int) $status;
    }

    return [
      'ok' => true,
      'status' => (int) $status,
      'body' => $jsonRes,
      'raw' => is_string($curlResponse) ? $curlResponse : null,
    ];
  }

  /**
   * Résout le statut d'un paiement (plusieurs références possibles).
   *
   * @param SessionPayment $payment Paiement local
   * @return array{
   *   paid: bool,
   *   pending: bool,
   *   cancelled: bool,
   *   status: int,
   *   message: string,
   *   checked_reference?: string,
   *   server_response?: array<string, mixed>|null
   * }
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
        'server_response' => null,
      ];
    }

    $references = array_values(array_unique(array_filter([
      $payment->reference,
      $payment->provider_reference,
    ])));

    $lastStatus = -1;
    $lastMessage = 'Statut en attente';
    $lastServerResponse = null;

    foreach ($references as $ref) {
      $result = $this->fetchStatus($ref);

      if (! $result['ok']) {
        $lastServerResponse = [
          'source' => 'flexpay_check',
          'reference' => $ref,
          'error' => $result['error'] ?? 'check_failed',
          'raw' => $result['raw'] ?? null,
        ];

        continue;
      }

      $status = $result['status'];
      $lastStatus = $status;
      $body = $result['body'] ?? [];
      $lastMessage = $body['message'] ?? $body['transaction']['message'] ?? $lastMessage;
      $lastServerResponse = [
        'source' => 'flexpay_check',
        'reference' => $ref,
        'http_status' => $status,
        'body' => $body,
        'raw' => $result['raw'] ?? null,
      ];

      if ($status === 0) {
        return [
          'paid' => true,
          'pending' => false,
          'cancelled' => false,
          'status' => 0,
          'message' => 'Paiement effectué avec succès',
          'checked_reference' => $ref,
          'server_response' => $lastServerResponse,
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
          'server_response' => $lastServerResponse,
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
          'server_response' => $lastServerResponse,
        ];
      }
    }

    return [
      'paid' => false,
      'pending' => $lastStatus === 2,
      'cancelled' => $lastStatus === 1,
      'status' => $lastStatus,
      'message' => $lastMessage,
      'server_response' => $lastServerResponse,
    ];
  }
}
