<?php

namespace App\Support;

/**
 * Formate le corps des e-mails Academy (liens cliquables, retours à la ligne).
 */
class EmailBodyFormatter
{
  /**
   * Convertit un texte brut en HTML sûr avec liens cliquables.
   *
   * @param string $plainBody Texte du modèle (variables déjà remplacées)
   * @return string HTML pour insertion dans la vue e-mail
   */
  public static function bodyToHtml(string $plainBody): string
  {
    $escaped = htmlspecialchars($plainBody, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $withBreaks = nl2br($escaped, false);

    $linked = preg_replace_callback(
      '~\bhttps?://[^\s<>"\'\]]+~i',
      function (array $matches): string {
        $raw = $matches[0];
        $url = rtrim($raw, '.,);]\'"');
        $suffix = substr($raw, strlen($url));
        $safeUrl = htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return '<a href="'.$safeUrl.'" style="color:#c87832;font-weight:600;word-break:break-all;text-decoration:underline;">'
          .$safeUrl
          .'</a>'
          .htmlspecialchars($suffix, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
      },
      $withBreaks
    );

    return $linked ?? $withBreaks;
  }
}
