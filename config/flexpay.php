<?php

/**
 * Configuration FlexPay — types API et opérateurs Mobile Money (UI).
 *
 * @see docs/integration-paiement-flexpay/08-MOBILE-MONEY-CORRECTIFS.md
 */
return [

  /**
   * Type API FlexPay pour Mobile Money (doc v1.4 — toujours "1").
   */
  'flexpay_mobile_money_api_type' => env('FLEXPAY_MOBILE_MONEY_API_TYPE', '1'),

  /**
   * Type API FlexPay pour carte (si routé via paymentService).
   */
  'flexpay_card_api_type' => env('FLEXPAY_CARD_API_TYPE', '2'),

  /**
   * Opérateurs affichés côté UI (identifiants internes, pas le type API FlexPay).
   *
   * @return list<array{type: string, code: string, label: string, msisdn_regex: string}>
   */
  'flexpay_mobile_providers' => (function (): array {
    $raw = env('FLEXPAY_MOBILE_PROVIDERS');

    if ($raw) {
      $decoded = json_decode((string) $raw, true);

      if (is_array($decoded) && $decoded !== []) {
        return $decoded;
      }
    }

    return [
      ['type' => 'mpesa', 'code' => 'mpesa', 'label' => 'M-Pesa', 'msisdn_regex' => '^2438[123][0-9]{7}$'],
      ['type' => 'airtel', 'code' => 'airtel', 'label' => 'Airtel Money', 'msisdn_regex' => '^2439[0-9]{8}$'],
      ['type' => 'orange', 'code' => 'orange', 'label' => 'Orange Money', 'msisdn_regex' => '^2438[459][0-9]{7}$'],
      ['type' => 'afrimoney', 'code' => 'afrimoney', 'label' => 'Afri Money', 'msisdn_regex' => '^2439[0-9]{8}$'],
    ];
  })(),

];
