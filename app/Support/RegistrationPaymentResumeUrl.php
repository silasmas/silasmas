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

    return FrontendUrl::to("academy/{$slug}").'?reprendre='.urlencode((string) $registration->access_token);
  }
}
