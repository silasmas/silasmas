<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Construit des URLs publiques pour les fichiers du disque storage/app/public.
 */
class MediaUrl
{
  /**
   * Normalise une valeur stockée (chaîne, JSON, tableau Filament).
   *
   * @param mixed $value Chemin ou payload Filament
   * @return string|null Chemin relatif au disque public (ex. images/academy/x.jpg)
   */
  public static function normalizeStoredPath(mixed $value): ?string
  {
    if ($value === null || $value === '') {
      return null;
    }

    if (is_array($value)) {
      $value = $value[0] ?? null;
    }

    if (! is_string($value)) {
      return null;
    }

    $trimmed = trim($value);

    if ($trimmed === '') {
      return null;
    }

    if (str_starts_with($trimmed, '[') || str_starts_with($trimmed, '{')) {
      $decoded = json_decode($trimmed, true);
      if (is_array($decoded)) {
        if (isset($decoded[0]) && is_string($decoded[0])) {
          $trimmed = $decoded[0];
        } elseif (isset($decoded['path']) && is_string($decoded['path'])) {
          $trimmed = $decoded['path'];
        } else {
          $first = reset($decoded);
          $trimmed = is_string($first) ? $first : null;
        }
      }
    }

    if ($trimmed === null || $trimmed === '') {
      return null;
    }

    $trimmed = str_replace('\\', '/', $trimmed);
    $trimmed = ltrim($trimmed, '/');

    if (str_starts_with($trimmed, 'storage/')) {
      $trimmed = substr($trimmed, strlen('storage/'));
    }

    return $trimmed;
  }

  /**
   * Retourne l'URL absolue accessible depuis le navigateur.
   *
   * @param mixed $value Chemin stocké en base
   * @return string|null URL complète ou null
   */
  public static function publicUrl(mixed $value): ?string
  {
    $path = self::normalizeStoredPath($value);

    if ($path === null) {
      return null;
    }

    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
      return $path;
    }

    $baseUrl = rtrim(config('app.url'), '/');

    if (str_starts_with($path, 'assets/')) {
      return $baseUrl.'/'.$path;
    }

    $storageUrl = Storage::disk('public')->url($path);

    if (str_starts_with($storageUrl, 'http://') || str_starts_with($storageUrl, 'https://')) {
      return $storageUrl;
    }

    return $baseUrl.'/'.ltrim($storageUrl, '/');
  }

  /**
   * Convertit une URL YouTube en URL d'intégration iframe.
   *
   * @param string $url URL YouTube
   * @return string|null URL embed
   */
  public static function youtubeEmbedUrl(string $url, ?string $origin = null): ?string
  {
    $videoId = self::youtubeVideoId($url);

    if ($videoId === null) {
      return null;
    }

    $origin = rtrim($origin ?? FrontendUrl::base(), '/');
    $query = http_build_query([
      'rel' => '0',
      'modestbranding' => '1',
      'playsinline' => '1',
      'origin' => $origin,
    ]);

    return 'https://www.youtube-nocookie.com/embed/'.$videoId.'?'.$query;
  }

  /**
   * Extrait l'identifiant d'une vidéo YouTube.
   *
   * @param string $url URL YouTube
   * @return string|null ID vidéo ou null
   */
  public static function youtubeVideoId(string $url): ?string
  {
    if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/|live\/))([\w-]{11})/', $url, $matches)) {
      return $matches[1];
    }

    return null;
  }

  /**
   * URL de miniature YouTube haute définition.
   *
   * @param string $url URL YouTube
   * @return string|null URL miniature
   */
  public static function youtubeThumbnailUrl(string $url): ?string
  {
    $videoId = self::youtubeVideoId($url);

    if ($videoId === null) {
      return null;
    }

    return 'https://img.youtube.com/vi/'.$videoId.'/hqdefault.jpg';
  }

  /**
   * URL de visionnage YouTube (page publique).
   *
   * @param string $url URL YouTube
   * @return string|null URL watch
   */
  public static function youtubeWatchUrl(string $url): ?string
  {
    $videoId = self::youtubeVideoId($url);

    if ($videoId === null) {
      return null;
    }

    return 'https://www.youtube.com/watch?v='.$videoId;
  }

  /**
   * Convertit une URL Vimeo en URL d'intégration iframe.
   *
   * @param string $url URL Vimeo
   * @return string|null URL embed
   */
  public static function vimeoEmbedUrl(string $url): ?string
  {
    if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
      return 'https://player.vimeo.com/video/'.$matches[1];
    }

    return null;
  }
}
