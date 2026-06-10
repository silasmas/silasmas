<?php

namespace App\Support;

/**
 * Référentiel des opérateurs Mobile Money proposés à l'inscription.
 */
class MobileMoneyOperators
{
  /**
   * Identifiants des opérateurs supportés.
   *
   * @var list<string>
   */
  public const ALL = ['mpesa', 'airtel', 'orange', 'afrimoney'];

  /**
   * Libellés affichés dans Filament et l'API.
   *
   * @return array<string, string>
   */
  public static function labels(): array
  {
    return [
      'mpesa' => 'M-Pesa (Vodacom)',
      'airtel' => 'Airtel Money',
      'orange' => 'Orange Money',
      'afrimoney' => 'Afrimoney (Africell)',
    ];
  }

  /**
   * Filtre et normalise une liste d'opérateurs activés.
   *
   * @param array<int, string>|null $raw Liste brute
   * @return list<string> Opérateurs valides
   */
  public static function normalizeEnabled(?array $raw): array
  {
    if ($raw === null) {
      return self::ALL;
    }

    $enabled = [];

    foreach ($raw as $operator) {
      if (is_string($operator) && in_array($operator, self::ALL, true)) {
        $enabled[] = $operator;
      }
    }

    return array_values(array_unique($enabled));
  }
}
