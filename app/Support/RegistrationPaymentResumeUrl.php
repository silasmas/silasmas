<?php

namespace App\Support;

use App\Models\Registration;

/**
 * Génère l'URL frontend pour reprendre une inscription au paiement.
 */
class RegistrationPaymentResumeUrl
{
  /**
   * URL absolue vers la page de finalisation paiement.
   *
   * @param Registration $registration Inscription en attente de paiement
   * @return string URL complète (ex. https://silasmas.com/academy/slug/finaliser/token)
   */
  public static function frontendUrl(Registration $registration): string
  {
    $registration->loadMissing('trainingSession');
    $registration->ensureAccessToken();

    $slug = $registration->trainingSession?->slug ?? '';

    if ($slug === '') {
      return FrontendUrl::to('academy');
    }

    return FrontendUrl::to("academy/{$slug}/finaliser/{$registration->access_token}");
  }

  /**
   * Indique si le texte contient déjà un lien de finalisation ou reprise valide.
   */
  public static function bodyHasPaymentResumeLink(string $body): bool
  {
    return (bool) preg_match(
      '~https?://[^\s<>"\'\]]+/academy/[^/\s<>"\'\]]+/(?:finaliser|reprendre)/[A-Za-z0-9]+~i',
      $body
    );
  }

  /**
   * Remplace uniquement les anciens liens #inscription (sans toucher aux URLs /finaliser/).
   *
   * @param string $body Corps du message (variables déjà remplacées)
   * @param string $resumeUrl Lien /finaliser/{jeton}
   * @param string|null $sessionSlug Slug session pour cibler les URLs en dur
   * @return string Corps corrigé
   */
  public static function replaceLegacyInscriptionLinks(
    string $body,
    string $resumeUrl,
    ?string $sessionSlug = null
  ): string {
    if (self::bodyHasPaymentResumeLink($body)) {
      return $body;
    }

    if ($sessionSlug !== null && $sessionSlug !== '') {
      $wrongLinks = [
        FrontendUrl::to("academy/{$sessionSlug}#inscription"),
        FrontendUrl::to("academy/{$sessionSlug}#inscription**"),
        rtrim(FrontendUrl::to("academy/{$sessionSlug}"), '/').'#inscription',
      ];

      foreach ($wrongLinks as $wrongLink) {
        $body = str_replace($wrongLink, $resumeUrl, $body);
      }

      if (self::bodyHasPaymentResumeLink($body)) {
        return $body;
      }

      $escapedSlug = preg_quote($sessionSlug, '~');
      $baseHost = preg_quote(parse_url(FrontendUrl::base(), PHP_URL_HOST) ?? 'silasmas.com', '~');
      $hostPattern = '(?:www\.)?(?:'.$baseHost.'|silasmas\.com)';

      // Uniquement les URLs se terminant par #inscription (pas les préfixes de /finaliser/).
      $body = preg_replace(
        '~https?://'.$hostPattern.'/academy/'.$escapedSlug.'/?#inscription\*{0,2}~i',
        $resumeUrl,
        $body
      ) ?? $body;

      // Page session seule (sans /finaliser/ ni /reprendre/ après le slug).
      $body = preg_replace(
        '~https?://'.$hostPattern.'/academy/'.$escapedSlug.'/?(?![/\w#])~i',
        $resumeUrl,
        $body
      ) ?? $body;
    }

    if (str_contains($body, '#inscription') && ! self::bodyHasPaymentResumeLink($body)) {
      $body = preg_replace(
        '~https?://[^\s<>"\'\]]+#inscription\*{0,2}~i',
        $resumeUrl,
        $body
      ) ?? $body;
    }

    if (! self::bodyHasPaymentResumeLink($body) && ! str_contains($body, $resumeUrl)) {
      $body = trim($body)."\n\n".$resumeUrl;
    }

    return $body;
  }
}
