<?php

namespace App\Support;

/**
 * URL de base du site public Next.js (e-mails, liens participant, partage, FlexPay).
 */
class FrontendUrl
{
  /** URL publique du site vitrine en production. */
  private const PRODUCTION_BASE = 'https://silasmas.com';

  /**
   * Retourne l'URL racine du frontend sans slash final.
   *
   * @return string URL absolue (ex. https://silasmas.com)
   */
  public static function base(): string
  {
    $configured = rtrim((string) config('app.frontend_url', ''), '/');
    $appUrl = rtrim((string) config('app.url', ''), '/');

    if ($configured !== '' && self::isLocalHostUrl($configured)) {
      if (self::isLocalDevRuntime($appUrl)) {
        return $configured;
      }
    } elseif ($configured !== '') {
      return $configured;
    }

    if (self::isProductionApiHost($appUrl)) {
      return self::PRODUCTION_BASE;
    }

    if (self::isLocalDevRuntime($appUrl)) {
      return $configured !== '' ? $configured : 'http://localhost:3000';
    }

    return self::PRODUCTION_BASE;
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
   * Indique si l'URL pointe vers localhost ou 127.0.0.1.
   *
   * @param string $url URL à tester
   * @return bool true si URL locale
   */
  protected static function isLocalHostUrl(string $url): bool
  {
    return (bool) preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?(/|$)#i', $url);
  }

  /**
   * Indique si l'API tourne en environnement de développement local.
   *
   * @param string $appUrl APP_URL configurée
   * @return bool true en dev local
   */
  protected static function isLocalDevRuntime(string $appUrl): bool
  {
    if (app()->environment('local')) {
      return true;
    }

    return $appUrl !== '' && self::isLocalHostUrl($appUrl);
  }

  /**
   * Indique si l'API est hébergée sur le domaine Silasmas production.
   *
   * @param string $appUrl APP_URL configurée
   * @return bool true sur api.silasmas.com ou silasmas.com
   */
  protected static function isProductionApiHost(string $appUrl): bool
  {
    if ($appUrl === '') {
      return false;
    }

    $host = parse_url($appUrl, PHP_URL_HOST);

    if (! is_string($host) || $host === '') {
      return false;
    }

    $host = strtolower($host);

    return $host === 'api.silasmas.com'
      || $host === 'silasmas.com'
      || str_ends_with($host, '.silasmas.com');
  }
}
