<?php

namespace App\Support;

use App\Models\Registration;

/**
 * Génère l'URL frontend pour reprendre une inscription au paiement.
 */
class RegistrationPaymentResumeUrl
{
  /**
   * URL absolue vers le formulaire d'inscription, étape paiement préremplie.
   *
   * @param Registration $registration Inscription en attente de paiement
   * @return string URL complète (ex. https://silasmas.com/academy/slug?reprendre=token)
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
   * Remplace les anciens liens #inscription par l'URL de reprise paiement.
   *
   * @param string $body Corps du message (variables déjà remplacées)
   * @param string $resumeUrl Lien /reprendre/{jeton}
   * @param string|null $sessionSlug Slug session pour cibler les URLs en dur
   * @return string Corps corrigé
   */
  public static function replaceLegacyInscriptionLinks(
    string $body,
    string $resumeUrl,
    ?string $sessionSlug = null
  ): string {
    if ($sessionSlug !== null && $sessionSlug !== '') {
      $escapedSlug = preg_quote($sessionSlug, '~');
      $baseHost = preg_quote(parse_url(FrontendUrl::base(), PHP_URL_HOST) ?? 'silasmas.com', '~');

      $patterns = [
        '~https?://(?:www\.)?'.$baseHost.'/academy/'.$escapedSlug.'/?(?:#inscription)?\*{0,2}~i',
        '~https?://(?:www\.)?silasmas\.com/academy/'.$escapedSlug.'/?(?:#inscription)?\*{0,2}~i',
      ];

      foreach ($patterns as $pattern) {
        $body = preg_replace($pattern, $resumeUrl, $body) ?? $body;
      }

      $wrongLinks = [
        FrontendUrl::to("academy/{$sessionSlug}#inscription"),
        FrontendUrl::to("academy/{$sessionSlug}#inscription**"),
        FrontendUrl::to("academy/{$sessionSlug}"),
        rtrim(FrontendUrl::to("academy/{$sessionSlug}"), '/').'#inscription',
      ];

      foreach ($wrongLinks as $wrongLink) {
        $body = str_replace($wrongLink, $resumeUrl, $body);
      }
    }

    if (str_contains($body, '#inscription') && ! str_contains($body, '/reprendre/')) {
      $body = preg_replace(
        '~https?://[^\s<>"\'\]]*#inscription\*{0,2}~i',
        $resumeUrl,
        $body
      ) ?? $body;
    }

    if (! str_contains($body, $resumeUrl)) {
      $body = trim($body)."\n\n".$resumeUrl;
    }

    return $body;
  }
}
