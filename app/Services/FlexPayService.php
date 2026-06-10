<?php

namespace App\Services;

use App\Support\FrontendUrl;

/**
 * Service FlexPay — initiation des paiements par carte bancaire.
 */
class FlexPayService
{
  /**
   * Initie un paiement par carte et retourne l'URL de redirection FlexPay.
   *
   * @param float $amount Montant
   * @param string $currency Devise (USD, CDF)
   * @param string $reference Référence transaction
   * @param string $description Libellé affiché
   * @return array{rep: bool, url?: string, orderNumber?: string, message?: string}
   */
  public function initiatePayment(
    float $amount,
    string $currency,
    string $reference,
    string $description
  ): array {
    $token = config('services.flexpay.token');

    if (empty($token)) {
      throw new \RuntimeException('Le token FlexPay est vide. Vérifiez FLEXPAY_API_TOKEN dans .env.');
    }

    $apiUrl = rtrim(config('app.url'), '/');
    $baseRedirectUrl = "{$apiUrl}/academy/payment/return/{$reference}/{$amount}/{$currency}";

    $body = [
      'authorization' => 'Bearer '.$token,
      'merchant' => config('services.flexpay.merchant'),
      'reference' => $reference,
      'amount' => $amount,
      'currency' => $currency,
      'description' => $description,
      'callback_url' => $apiUrl.'/academy/payment/callback',
      'approve_url' => "{$baseRedirectUrl}/success",
      'cancel_url' => "{$baseRedirectUrl}/cancel",
      'decline_url' => "{$baseRedirectUrl}/decline",
      'home_url' => FrontendUrl::base(),
    ];

    $curl = curl_init(config('services.flexpay.gateway_card'));
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $curlResponse = curl_exec($curl);
    curl_close($curl);

    $json = json_decode($curlResponse, true);

    if (isset($json['code']) && (string) $json['code'] === '0') {
      return [
        'rep' => true,
        'url' => $json['url'],
        'orderNumber' => $json['orderNumber'],
        'data' => $json,
      ];
    }

    return [
      'rep' => false,
      'message' => $json['message'] ?? 'Réponse invalide de l\'API FlexPay',
      'server_response' => [
        'source' => 'flexpay_card',
        'body' => is_array($json) ? $json : null,
        'raw' => is_string($curlResponse) ? $curlResponse : null,
      ],
    ];
  }
}
