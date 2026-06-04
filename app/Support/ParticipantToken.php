<?php

namespace App\Support;

use App\Models\Registration;
use Illuminate\Support\Str;

/**
 * Génération de jetons d'accès à l'espace participant.
 */
class ParticipantToken
{
  /**
   * Crée un jeton unique pour une inscription.
   *
   * @return string Jeton URL-safe
   */
  public static function generate(): string
  {
    do {
      $token = Str::random(48);
    } while (Registration::where('access_token', $token)->exists());

    return $token;
  }

  /**
   * URL de l'espace participant côté front Next.js.
   *
   * @param Registration $registration Inscription confirmée
   * @return string URL absolue
   */
  public static function frontendUrl(Registration $registration): string
  {
    $base = rtrim(config('services.flexpay.frontend_url', env('FRONTEND_URL', config('app.url'))), '/');
    $token = $registration->access_token ?? '';

    return "{$base}/academy/espace/{$token}";
  }
}
