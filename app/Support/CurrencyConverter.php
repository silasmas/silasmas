<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * Conversion USD ↔ CDF selon le taux défini dans le paramétrage du site.
 */
class CurrencyConverter
{
  /**
   * Taux 1 USD = X CDF (depuis la base ou défaut).
   */
  public static function usdToCdfRate(): ?float
  {
    $rate = SiteSetting::instance()->usd_to_cdf_rate;

    if ($rate === null || (float) $rate <= 0) {
      return null;
    }

    return (float) $rate;
  }

  /**
   * Calcule les montants affichés USD et CDF pour un prix de session.
   *
   * @param float $amount Montant enregistré
   * @param string $currency Devise enregistrée (USD|CDF)
   * @return array{usd: float|null, cdf: float|null, rate: float|null}
   */
  public static function dualAmounts(float $amount, string $currency): array
  {
    $rate = self::usdToCdfRate();
    $currency = strtoupper($currency);

    if ($rate === null) {
      return [
        'usd' => $currency === 'USD' ? $amount : null,
        'cdf' => $currency === 'CDF' ? $amount : null,
        'rate' => null,
      ];
    }

    if ($currency === 'USD') {
      return [
        'usd' => round($amount, 2),
        'cdf' => round($amount * $rate, 2),
        'rate' => $rate,
      ];
    }

    if ($currency === 'CDF') {
      return [
        'usd' => round($amount / $rate, 2),
        'cdf' => round($amount, 2),
        'rate' => $rate,
      ];
    }

    return ['usd' => null, 'cdf' => null, 'rate' => $rate];
  }

  /**
   * Libellé lisible des deux devises.
   *
   * @param float $amount Montant session
   * @param string $currency Devise session
   * @return string|null Ex. « 10 USD (28 500 CDF) »
   */
  public static function formatDualLabel(float $amount, string $currency): ?string
  {
    $dual = self::dualAmounts($amount, $currency);

    if ($dual['usd'] !== null && $dual['cdf'] !== null) {
      return number_format($dual['usd'], 2, ',', ' ').' USD ('.
        number_format($dual['cdf'], 0, ',', ' ').' CDF)';
    }

    if ($dual['usd'] !== null) {
      return number_format($dual['usd'], 2, ',', ' ').' USD';
    }

    if ($dual['cdf'] !== null) {
      return number_format($dual['cdf'], 0, ',', ' ').' CDF';
    }

    return null;
  }
}
