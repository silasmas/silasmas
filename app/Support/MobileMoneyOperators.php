<?php

namespace App\Support;

/**
 * Référentiel des opérateurs Mobile Money proposés à l'inscription.
 *
 * Les codes (mpesa, airtel…) servent à l'UI et à la validation locale.
 * FlexPay reçoit toujours type API "1" — voir config/flexpay.php.
 */
class MobileMoneyOperators
{
  /**
   * Alias historiques vers les codes internes du projet.
   *
   * @var array<string, string>
   */
  protected const CODE_ALIASES = [
    'afri' => 'afrimoney',
  ];

  /**
   * Identifiants des opérateurs supportés par défaut.
   *
   * @var list<string>
   */
  public const ALL = ['mpesa', 'airtel', 'orange', 'afrimoney'];

  /**
   * Liste des opérateurs configurés (UI).
   *
   * @return list<array{type: string, code: string, label: string, msisdn_regex?: string}>
   */
  public static function configuredProviders(): array
  {
    $providers = config('flexpay.flexpay_mobile_providers', []);

    if (! is_array($providers) || $providers === []) {
      return self::defaultProviders();
    }

    $normalized = [];

    foreach ($providers as $provider) {
      if (! is_array($provider)) {
        continue;
      }

      $code = self::normalizeCode((string) ($provider['code'] ?? $provider['type'] ?? ''));

      if ($code === null) {
        continue;
      }

      $normalized[] = [
        'type' => $code,
        'code' => $code,
        'label' => (string) ($provider['label'] ?? self::labels()[$code] ?? $code),
        'msisdn_regex' => $provider['msisdn_regex'] ?? null,
      ];
    }

    return $normalized !== [] ? $normalized : self::defaultProviders();
  }

  /**
   * Codes opérateurs valides pour validation API.
   *
   * @return list<string>
   */
  public static function supportedCodes(): array
  {
    return array_values(array_unique(array_map(
      fn (array $provider): string => $provider['code'],
      self::configuredProviders()
    )));
  }

  /**
   * Libellés affichés dans Filament et l'API.
   *
   * @return array<string, string>
   */
  public static function labels(): array
  {
    $labels = [];

    foreach (self::configuredProviders() as $provider) {
      $labels[$provider['code']] = $provider['label'];
    }

    return $labels !== [] ? $labels : [
      'mpesa' => 'M-Pesa (Vodacom)',
      'airtel' => 'Airtel Money',
      'orange' => 'Orange Money',
      'afrimoney' => 'Afrimoney (Africell)',
    ];
  }

  /**
   * Filtre et normalise une liste d'opérateurs activés pour une session.
   *
   * @param array<int, string>|null $raw Liste brute
   * @return list<string> Opérateurs valides
   */
  public static function normalizeEnabled(?array $raw): array
  {
    if ($raw === null) {
      return self::supportedCodes();
    }

    $enabled = [];
    $allowed = self::supportedCodes();

    foreach ($raw as $operator) {
      if (! is_string($operator)) {
        continue;
      }

      $code = self::normalizeCode($operator);

      if ($code !== null && in_array($code, $allowed, true)) {
        $enabled[] = $code;
      }
    }

    return array_values(array_unique($enabled));
  }

  /**
   * Normalise un code opérateur (ex. afri → afrimoney).
   *
   * @param string $code Code brut
   * @return string|null Code interne ou null
   */
  public static function normalizeCode(string $code): ?string
  {
    $code = strtolower(trim($code));

    if ($code === '') {
      return null;
    }

    $code = self::CODE_ALIASES[$code] ?? $code;

    if (! in_array($code, self::ALL, true)) {
      return null;
    }

    return $code;
  }

  /**
   * Fournisseurs par défaut si config absente.
   *
   * @return list<array{type: string, code: string, label: string, msisdn_regex: string}>
   */
  protected static function defaultProviders(): array
  {
    return [
      ['type' => 'mpesa', 'code' => 'mpesa', 'label' => 'M-Pesa', 'msisdn_regex' => '^2438[123][0-9]{7}$'],
      ['type' => 'airtel', 'code' => 'airtel', 'label' => 'Airtel Money', 'msisdn_regex' => '^2439[0-9]{8}$'],
      ['type' => 'orange', 'code' => 'orange', 'label' => 'Orange Money', 'msisdn_regex' => '^2438[459][0-9]{7}$'],
      ['type' => 'afrimoney', 'code' => 'afrimoney', 'label' => 'Afri Money', 'msisdn_regex' => '^2439[0-9]{8}$'],
    ];
  }
}
