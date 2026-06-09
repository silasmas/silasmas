<?php

namespace App\Support;

/**
 * URL de base du site public Next.js (liens participants, partage, retours paiement).
 */
class FrontendUrl
{
  /**
   * Retourne l'URL racine du frontend sans slash final.
   *
   * @return string URL absolue (ex. https://silasmas.com)
   */
  public static function base(): string
  {
    $configured = rtrim((string) config('app.frontend_url', ''), '/');

    if (self::isUsable($configured)) {
      return $configured;
    }

    if (app()->environment('production')) {
      return 'https://silasmas.com';
    }

    return rtrim((string) config('app.url', 'http://localhost'), '/');
  }

  /**
   * Construit une URL absolue vers une route du frontend.
   *
   * @param string $path Chemin relatif (avec ou sans / initial)
   * @return string URL complète
   */
  public static function to(string $path): string
  {
    return self::base().'/'.ltrim($path, '/');
  }

  /**
   * Indique si l'URL configurée est exploitable en production.
   *
   * @param string $url URL candidate
   * @return bool true si utilisable
   */
  protected static function isUsable(string $url): bool
  {
    if ($url === '') {
      return false;
    }

    if (! app()->environment('production')) {
      return true;
    }

    return ! preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?(/|$)#i', $url);
  }
}
