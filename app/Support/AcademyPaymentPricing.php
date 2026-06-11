<?php

namespace App\Support;

use App\Models\TrainingSession;

/**
 * Tarification d'inscription : équivalents de devise sans changer le montant session.
 */
class AcademyPaymentPricing
{
  /**
   * Devises proposées au paiement pour une session payante.
   *
   * @param TrainingSession $session Session concernée
   * @return list<array{currency: string, amount: float, formatted: string}>
   */
  public static function currencyOptions(TrainingSession $session): array
  {
    if (! $session->isPaid()) {
      return [];
    }

    $baseAmount = $session->registrationAmount();
    $baseCurrency = strtoupper($session->registrationCurrency());
    $dual = CurrencyConverter::dualAmounts($baseAmount, $baseCurrency);
    $options = [];

    if ($dual['usd'] !== null) {
      $options[] = self::option('USD', (float) $dual['usd']);
    }

    if ($dual['cdf'] !== null) {
      $options[] = self::option('CDF', (float) round($dual['cdf'], 0));
    }

    if ($options === []) {
      $options[] = self::option($baseCurrency, $baseAmount);
    }

    return self::uniqueByCurrency($options);
  }

  /**
   * Codes devises autorisés pour une session.
   *
   * @param TrainingSession $session Session concernée
   * @return list<string>
   */
  public static function allowedCurrencyCodes(TrainingSession $session): array
  {
    return array_column(self::currencyOptions($session), 'currency');
  }

  /**
   * Résout montant et devise à facturer selon le choix participant.
   *
   * @param TrainingSession $session Session tarifée
   * @param string $requestedCurrency Devise choisie (USD, CDF…)
   * @return array{amount: float, currency: string}
   */
  public static function resolveCharge(TrainingSession $session, string $requestedCurrency): array
  {
    $requestedCurrency = strtoupper(trim($requestedCurrency));
    $options = self::currencyOptions($session);

    foreach ($options as $option) {
      if ($option['currency'] === $requestedCurrency) {
        return [
          'amount' => (float) $option['amount'],
          'currency' => $option['currency'],
        ];
      }
    }

    throw new \InvalidArgumentException('Cette devise n\'est pas disponible pour cette session.');
  }

  /**
   * Formate une option de devise pour l'API.
   *
   * @param string $currency Code devise
   * @param float $amount Montant
   * @return array{currency: string, amount: float, formatted: string}
   */
  protected static function option(string $currency, float $amount): array
  {
    $currency = strtoupper($currency);

    return [
      'currency' => $currency,
      'amount' => $amount,
      'formatted' => self::formatAmount($amount, $currency).' '.$currency,
    ];
  }

  /**
   * Affiche un montant selon la devise.
   *
   * @param float $amount Montant
   * @param string $currency Code devise
   * @return string Montant formaté sans suffixe devise
   */
  protected static function formatAmount(float $amount, string $currency): string
  {
    if ($currency === 'CDF') {
      return number_format($amount, 0, ',', ' ');
    }

    return number_format($amount, 2, ',', ' ');
  }

  /**
   * Supprime les doublons de devise en conservant la première occurrence.
   *
   * @param list<array{currency: string, amount: float, formatted: string}> $options Options brutes
   * @return list<array{currency: string, amount: float, formatted: string}>
   */
  protected static function uniqueByCurrency(array $options): array
  {
    $seen = [];
    $unique = [];

    foreach ($options as $option) {
      if (isset($seen[$option['currency']])) {
        continue;
      }

      $seen[$option['currency']] = true;
      $unique[] = $option;
    }

    return $unique;
  }
}
