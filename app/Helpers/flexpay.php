<?php

use App\Models\SessionPayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

if (! function_exists('generateAcademyPaymentReference')) {
  /**
   * Génère une référence unique pour un paiement Academy.
   *
   * @return string Référence type ACAD-XXXXXXXXXX
   */
  function generateAcademyPaymentReference(): string
  {
    do {
      $reference = 'ACAD-'.strtoupper(Str::random(10));
    } while (SessionPayment::where('reference', $reference)->exists());

    return $reference;
  }
}

if (! function_exists('flexpayMobileTypeForOperator')) {
  /**
   * Code type FlexPay selon l'opérateur Mobile Money choisi.
   *
   * @param string|null $operator mpesa|airtel|orange|afrimoney
   * @return string Code envoyé à l'API FlexPay
   */
  function flexpayMobileTypeForOperator(?string $operator): string
  {
    $map = config('services.flexpay.mobile_types', []);

    return $map[$operator] ?? $map['default'] ?? '1';
  }
}

if (! function_exists('initRequeteFlexPayMobile')) {
  /**
   * Déclenche un paiement Mobile Money via FlexPay.
   *
   * @param array<string, mixed> $data merchant, type, phone, reference, amount, currency, callbackUrl
   * @param SessionPayment $payment Enregistrement à mettre à jour
   * @return array<string, mixed>
   */
  function initRequeteFlexPayMobile(array $data, SessionPayment $payment): array
  {
    $response = Http::withHeaders([
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer '.config('services.flexpay.token'),
    ])->post(config('services.flexpay.gateway_mobile'), $data);

    $responseBody = $response->json();

    if (isset($responseBody['code']) && (string) $responseBody['code'] === '0') {
      $payment->update([
        'provider_reference' => $responseBody['orderNumber'] ?? null,
        'status' => 'processing',
        'channel' => 'mobile_money',
      ]);

      return [
        'reponse' => true,
        'message' => 'Paiement en attente. Validez la demande sur votre téléphone.',
        'type' => 'mobile',
        'reference' => $payment->reference,
        'orderNumber' => $responseBody['orderNumber'] ?? $payment->reference,
      ];
    }

    return [
      'reponse' => false,
      'message' => $responseBody['message'] ?? 'Échec de la transaction Mobile Money',
      'server_response' => [
        'source' => 'flexpay_mobile',
        'http_status' => $response->status(),
        'body' => $responseBody ?? $response->body(),
      ],
    ];
  }
}
