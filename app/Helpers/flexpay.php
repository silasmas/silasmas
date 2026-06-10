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

if (! function_exists('flexpayMobileGatewayUrl')) {
  /**
   * URL de l'API Mobile Money FlexPay (sans espaces parasites).
   *
   * @return string URL gateway
   */
  function flexpayMobileGatewayUrl(): string
  {
    return trim((string) config('services.flexpay.gateway_mobile'));
  }
}

if (! function_exists('flexpayUsesPaymentServiceEndpoint')) {
  /**
   * Indique si l'URL configurée pointe vers paymentService (format JSON différent).
   *
   * @return bool true si endpoint paymentService
   */
  function flexpayUsesPaymentServiceEndpoint(): bool
  {
    return str_contains(flexpayMobileGatewayUrl(), 'paymentService');
  }
}

if (! function_exists('flexpayMobileTypeForOperator')) {
  /**
   * Code type FlexPay selon l'opérateur (endpoint /mobile uniquement).
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

if (! function_exists('flexpayBuildMobilePayload')) {
  /**
   * Construit le corps JSON selon l'endpoint FlexPay configuré.
   *
   * paymentService : merchant_code, transaction_type=1, customer_phonenumber…
   * /mobile          : merchant, type (opérateur), phone…
   *
   * @param array<string, mixed> $data Données normalisées internes
   * @return array<string, mixed> Payload à envoyer à FlexPay
   */
  function flexpayBuildMobilePayload(array $data): array
  {
    if (flexpayUsesPaymentServiceEndpoint()) {
      return [
        'merchant_code' => $data['merchant'],
        'transaction_type' => '1',
        'your_reference' => $data['reference'],
        'customer_phonenumber' => $data['phone'],
        'amount' => $data['amount'],
        'currency' => $data['currency'],
        'callbackUrl' => $data['callbackUrl'],
      ];
    }

    return [
      'merchant' => $data['merchant'],
      'type' => $data['type'],
      'phone' => $data['phone'],
      'reference' => $data['reference'],
      'amount' => $data['amount'],
      'currency' => $data['currency'],
      'callbackUrl' => $data['callbackUrl'],
    ];
  }
}

if (! function_exists('flexpayExtractMobileErrorMessage')) {
  /**
   * Extrait le message d'erreur lisible depuis la réponse FlexPay.
   *
   * @param array<string, mixed>|null $responseBody Corps JSON FlexPay
   * @return string Message d'erreur
   */
  function flexpayExtractMobileErrorMessage(?array $responseBody): string
  {
    if ($responseBody === null) {
      return 'Échec de la transaction Mobile Money';
    }

    if (! empty($responseBody['message']) && is_string($responseBody['message'])) {
      return $responseBody['message'];
    }

    $code = $responseBody['code'] ?? null;

    if (is_string($code) && $code !== '' && $code !== '0' && ! is_numeric($code)) {
      return $code;
    }

    return 'Échec de la transaction Mobile Money';
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
    $payload = flexpayBuildMobilePayload($data);
    $gateway = flexpayMobileGatewayUrl();

    $response = Http::withHeaders([
      'Content-Type' => 'application/json',
      'Authorization' => 'Bearer '.config('services.flexpay.token'),
    ])->post($gateway, $payload);

    $responseBody = $response->json();
    $successCode = $responseBody['code'] ?? null;

    if ($successCode !== null && (string) $successCode === '0') {
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
      'message' => flexpayExtractMobileErrorMessage(is_array($responseBody) ? $responseBody : null),
      'server_response' => [
        'source' => 'flexpay_mobile',
        'endpoint' => $gateway,
        'payload_format' => flexpayUsesPaymentServiceEndpoint() ? 'paymentService' : 'mobile',
        'http_status' => $response->status(),
        'request' => $payload,
        'body' => $responseBody ?? $response->body(),
      ],
    ];
  }
}
