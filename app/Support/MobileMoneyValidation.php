<?php

namespace App\Support;

/**
 * Validation des numéros Mobile Money RDC (préfixe 243 + opérateur).
 *
 * La sélection d'opérateur sert uniquement à valider le MSISDN côté application.
 * FlexPay route le paiement via le numéro phone (type API toujours "1").
 */
class MobileMoneyValidation
{
  /**
   * Préfixes nationaux après 243 par opérateur (2 chiffres).
   *
   * @var array<string, string[]>
   */
  protected static array $operatorPrefixes = [
    'mpesa' => ['81', '82', '83', '84', '85', '89'],
    'airtel' => ['97', '98', '99'],
    'orange' => ['80', '84', '85', '86', '87', '88', '89'],
    'afrimoney' => ['90', '91', '99'],
  ];

  /**
   * Codes opérateurs supportés pour la validation Laravel.
   *
   * @return list<string>
   */
  public static function supportedOperatorCodes(): array
  {
    return MobileMoneyOperators::supportedCodes();
  }

  /**
   * Normalise un numéro (chiffres uniquement, préfixe 243).
   *
   * @param string $phone Numéro saisi
   * @return string|null Numéro normalisé ou null si invalide
   */
  public static function normalizePhone(string $phone): ?string
  {
    $digits = preg_replace('/\D+/', '', $phone) ?? '';

    if ($digits === '') {
      return null;
    }

    if (str_starts_with($digits, '00')) {
      $digits = substr($digits, 2);
    }

    if (str_starts_with($digits, '0') && strlen($digits) === 10) {
      $digits = '243'.substr($digits, 1);
    }

    if (! str_starts_with($digits, '243')) {
      return null;
    }

    if (strlen($digits) !== 12) {
      return null;
    }

    return $digits;
  }

  /**
   * Vérifie que le numéro correspond à l'opérateur choisi.
   *
   * @param string $phone Numéro brut
   * @param string $operator mpesa|airtel|orange|afrimoney
   * @return array{valid: bool, normalized?: string, message?: string}
   */
  public static function validateForOperator(string $phone, string $operator): array
  {
    $operator = MobileMoneyOperators::normalizeCode($operator) ?? $operator;
    $normalized = self::normalizePhone($phone);

    if ($normalized === null) {
      return [
        'valid' => false,
        'message' => 'Le numéro doit commencer par 243 et contenir 12 chiffres (ex. 243821234567).',
      ];
    }

    $regex = self::msisdnRegexForOperator($operator);

    if ($regex !== null) {
      if (! preg_match('~'.$regex.'~', $normalized)) {
        return [
          'valid' => false,
          'message' => 'Ce numéro ne correspond pas à '.self::operatorLabel($operator).'. Corrigez le numéro ou changez d\'opérateur.',
        ];
      }

      return [
        'valid' => true,
        'normalized' => $normalized,
      ];
    }

    $prefix = substr($normalized, 3, 2);
    $allowed = self::$operatorPrefixes[$operator] ?? [];

    if ($allowed !== [] && ! in_array($prefix, $allowed, true)) {
      return [
        'valid' => false,
        'message' => 'Ce numéro ne correspond pas à '.self::operatorLabel($operator).'. Corrigez le numéro ou changez d\'opérateur.',
      ];
    }

    return [
      'valid' => true,
      'normalized' => $normalized,
    ];
  }

  /**
   * Regex MSISDN configurée pour un opérateur.
   *
   * @param string $operator Code opérateur
   * @return string|null Pattern sans délimiteurs
   */
  protected static function msisdnRegexForOperator(string $operator): ?string
  {
    foreach (MobileMoneyOperators::configuredProviders() as $provider) {
      if ($provider['code'] === $operator && ! empty($provider['msisdn_regex'])) {
        return (string) $provider['msisdn_regex'];
      }
    }

    return null;
  }

  /**
   * Libellé lisible d'un opérateur.
   *
   * @param string $operator Code opérateur
   * @return string Libellé
   */
  protected static function operatorLabel(string $operator): string
  {
    return MobileMoneyOperators::labels()[$operator] ?? $operator;
  }
}
