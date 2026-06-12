<?php

namespace App\Support;

use Illuminate\Http\Request;
use Symfony\Component\Intl\Countries;

/**
 * Déduit le pays du visiteur depuis les en-têtes HTTP (CDN / proxy).
 */
class VisitorCountry
{
  /**
   * Résout le code et le nom du pays à partir de la requête.
   *
   * @param Request $request Requête HTTP entrante
   * @return array{code: string, name: string}
   */
  public static function fromRequest(Request $request): array
  {
    $code = $request->header('CF-IPCountry')
      ?? $request->header('X-Country-Code')
      ?? $request->header('CloudFront-Viewer-Country');

    if (! is_string($code) || strlen(trim($code)) !== 2) {
      return [
        'code' => 'XX',
        'name' => 'Inconnu',
      ];
    }

    $code = strtoupper(trim($code));

    if ($code === 'XX' || $code === 'T1') {
      return [
        'code' => 'XX',
        'name' => 'Inconnu',
      ];
    }

    try {
      $name = Countries::exists($code)
        ? Countries::getName($code, 'fr')
        : $code;
    } catch (\Throwable) {
      $name = $code;
    }

    return [
      'code' => $code,
      'name' => $name,
    ];
  }
}
