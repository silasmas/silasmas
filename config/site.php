<?php

/**
 * Paramètres globaux du site (repli si non définis en base).
 */
return [

  /**
   * Taux 1 USD = X CDF (utilisé si SiteSetting.usd_to_cdf_rate est vide).
   */
  'usd_to_cdf_rate' => (float) env('USD_TO_CDF_RATE', 2850),

];
