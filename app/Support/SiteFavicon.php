<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Résout le favicon configuré en admin ou le jeu par défaut sous public/assets/img/favicon.
 */
class SiteFavicon
{
  public const DEFAULT_DIRECTORY = 'assets/img/favicon';

  /**
   * Vérifie si le favicon configuré en base est présent et accessible.
   *
   * @param string|null $stored Chemin stocké en base (Filament)
   * @return bool True si le fichier configuré peut être servi
   */
  public static function configuredPathIsValid(?string $stored): bool
  {
    $path = MediaUrl::normalizeStoredPath($stored);

    if ($path === null) {
      return false;
    }

    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
      return true;
    }

    if (str_starts_with($path, 'assets/')) {
      return is_file(public_path($path));
    }

    return Storage::disk('public')->exists($path);
  }

  /**
   * URL absolue du favicon admin si valide, sinon null.
   *
   * @param string|null $stored Chemin stocké en base
   * @return string|null URL publique ou null
   */
  public static function configuredPublicUrl(?string $stored): ?string
  {
    if (! self::configuredPathIsValid($stored)) {
      return null;
    }

    return MediaUrl::publicUrl($stored);
  }

  /**
   * URL absolue du favicon effectif (configuré ou 32×32 par défaut).
   *
   * @param string|null $stored Chemin stocké en base
   * @return string URL publique
   */
  public static function resolvedPublicUrl(?string $stored): string
  {
    $configured = self::configuredPublicUrl($stored);

    if ($configured !== null) {
      return $configured;
    }

    return self::defaultAssetUrl('favicon-32x32.png');
  }

  /**
   * Indique si le jeu complet par défaut doit être utilisé (pas de favicon admin valide).
   *
   * @param string|null $stored Chemin stocké en base
   * @return bool True pour le jeu par défaut
   */
  public static function usesDefaultSet(?string $stored): bool
  {
    return ! self::configuredPathIsValid($stored);
  }

  /**
   * URL publique asset() pour un fichier du dossier favicon par défaut.
   *
   * @param string $filename Nom du fichier (ex. favicon.ico)
   * @return string URL absolue
   */
  public static function defaultAssetUrl(string $filename): string
  {
    return asset(self::DEFAULT_DIRECTORY.'/'.$filename);
  }
}
