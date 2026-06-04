<?php

namespace App\Support;

/**
 * Valide et normalise un numéro Mobile Money RDC (243…) selon l'opérateur.
 */
class MobileMoneyPhoneValidator
{
  /**
   * Préfixes nationaux après 243 par opérateur.
   *
   * @var array<string, list<string>>
   */
  private const OPERATOR_PREFIXES = [
    'mpesa' => ['81', '82', '83'],
    'airtel' => ['97', '98', '99'],
    'orange' => ['84', '85', '86', '89'],
    'afrimoney' => ['90', '91'],
  ];

  /**
   * Normalise un numéro en chiffres uniquement (sans +).
   *
   * @param string $phone Numéro saisi
   * @return string Chiffres uniquement
   */
  public static function normalizeDigits(string $phone): string
  {
    return preg_replace('/\D+/', '', $phone) ?? '';
  }

  /**
   * Valide le numéro pour l'opérateur choisi.
   *
   * @param string $operator mpesa|airtel|orange|afrimoney
   * @param string $phone Numéro saisi
   * @return array{valid: bool, phone?: string, message?: string}
   */
  public static function validate(string $operator, string $phone): array
  {
    $digits = self::normalizeDigits($phone);

    if ($digits === '') {
      return [
        'valid' => false,
        'message' => 'Indiquez votre numéro Mobile Money.',
      ];
    }

    if (str_starts_with($digits, '00')) {
      $digits = substr($digits, 2);
    }

    if (str_starts_with($digits, '0') && ! str_starts_with($digits, '243')) {
      $digits = '243'.ltrim($digits, '0');
    }

    if (! str_starts_with($digits, '243')) {
      return [
        'valid' => false,
        'message' => 'Le numéro doit commencer par 243 (ex. 24382XXXXXXX).',
      ];
    }

    if (strlen($digits) !== 12) {
      return [
        'valid' => false,
        'message' => 'Le numéro doit contenir 12 chiffres au format 243XXXXXXXXX.',
      ];
    }

    $nationalPrefix = substr($digits, 3, 2);
    $allowed = self::OPERATOR_PREFIXES[$operator] ?? null;

    if ($allowed === null) {
      return ['valid' => true, 'phone' => $digits];
    }

    if (! in_array($nationalPrefix, $allowed, true)) {
      $label = match ($operator) {
        'mpesa' => 'M-Pesa (Vodacom)',
        'airtel' => 'Airtel Money',
        'orange' => 'Orange Money',
        'afrimoney' => 'Afrimoney (Africell)',
        default => $operator,
      };

      return [
        'valid' => false,
        'message' => "Ce numéro ne correspond pas à {$label}. Corrigez le numéro ou changez d'opérateur.",
      ];
    }

    return ['valid' => true, 'phone' => $digits];
  }
}
