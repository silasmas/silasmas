<?php

namespace App\Support;

use App\Models\SessionPayment;

/**
 * Présentation lisible des échecs de paiement (e-mail admin, dashboard).
 */
class PaymentFailurePresenter
{
  /**
   * @param SessionPayment $payment Paiement en échec
   */
  public function __construct(private SessionPayment $payment)
  {
  }

  /**
   * Libellé français de la méthode de paiement.
   *
   * @return string Libellé affichable
   */
  public function paymentMethodLabel(): string
  {
    $method = $this->payment->payment_method ?? $this->payment->channel;

    return match ($method) {
      'mobile_money' => 'Mobile Money',
      'card' => 'Carte bancaire',
      'bank' => 'Virement bancaire',
      'cash' => 'Espèces',
      default => $method ?? '—',
    };
  }

  /**
   * Lignes structurées pour l'e-mail (label + valeur).
   *
   * @return array<int, array{label: string, value: string}>
   */
  public function serverResponseLines(): array
  {
    $raw = trim((string) ($this->payment->failure_server_response ?? ''));

    if ($raw === '') {
      return [];
    }

    $data = json_decode($raw, true);

    if (! is_array($data)) {
      return [
        ['label' => 'Réponse brute', 'value' => $raw],
      ];
    }

    $lines = [];
    $source = (string) ($data['source'] ?? '');

    if ($source !== '') {
      $lines[] = [
        'label' => 'Origine',
        'value' => self::serverSourceLabel($source),
      ];
    }

    if (isset($data['http_status']) && $source !== 'flexpay_check') {
      $lines[] = [
        'label' => 'Code HTTP',
        'value' => (string) $data['http_status'],
      ];
    }

    if (isset($data['return_status'])) {
      $lines[] = [
        'label' => 'Statut retour',
        'value' => self::returnStatusLabel((string) $data['return_status']),
      ];
    }

    if (! empty($data['reference'])) {
      $lines[] = [
        'label' => 'Référence FlexPay',
        'value' => (string) $data['reference'],
      ];
    }

    $this->appendFlexPayBodyLines($lines, $data['body'] ?? null, $source);

    $payload = $data['payload'] ?? null;

    if (is_array($payload)) {
      $this->appendFlexPayBodyLines($lines, $payload, 'flexpay_webhook');
    }

    if (isset($data['http_status']) && $source === 'flexpay_check') {
      $lines[] = [
        'label' => 'Statut transaction',
        'value' => self::transactionStatusLabel((int) $data['http_status']),
      ];
    }

    if (! empty($data['query']) && is_array($data['query'])) {
      foreach ($data['query'] as $key => $value) {
        if ($value === null || $value === '') {
          continue;
        }

        $lines[] = [
          'label' => 'Paramètre '.ucfirst((string) $key),
          'value' => is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE),
        ];
      }
    }

    if ($lines === [] && ! empty($data['raw']) && is_string($data['raw'])) {
      $lines[] = [
        'label' => 'Réponse brute',
        'value' => self::truncate($data['raw'], 280),
      ];
    }

    return $lines;
  }

  /**
   * Extrait les champs utiles du corps FlexPay.
   *
   * @param array<int, array{label: string, value: string}> $lines Lignes en cours
   * @param mixed $body Corps JSON FlexPay
   * @param string $source Code source
   * @return void
   */
  private function appendFlexPayBodyLines(array &$lines, mixed $body, string $source): void
  {
    if (! is_array($body)) {
      return;
    }

    if (array_key_exists('code', $body) && $body['code'] !== null && $body['code'] !== '') {
      $code = (string) $body['code'];
      $isNumericCode = is_numeric($code);

      $lines[] = [
        'label' => $isNumericCode ? 'Code FlexPay' : 'Erreur FlexPay',
        'value' => $isNumericCode ? $code.' — '.self::flexpayCodeLabel($code) : $code,
      ];
    }

    if (! empty($body['message'])) {
      $lines[] = [
        'label' => 'Message FlexPay',
        'value' => (string) $body['message'],
      ];
    }

    if (! empty($body['orderNumber'])) {
      $lines[] = [
        'label' => 'N° commande',
        'value' => (string) $body['orderNumber'],
      ];
    }

    if ($source === 'flexpay_webhook' && isset($body['status'])) {
      $lines[] = [
        'label' => 'Statut webhook',
        'value' => (string) $body['status'],
      ];
    }
  }

  /**
   * Libellé français de la source technique.
   *
   * @param string $source Code source
   * @return string Libellé
   */
  public static function serverSourceLabel(string $source): string
  {
    return match ($source) {
      'flexpay_mobile' => 'FlexPay — Mobile Money',
      'flexpay_card' => 'FlexPay — Carte bancaire',
      'flexpay_card_return' => 'Retour navigateur (carte)',
      'flexpay_webhook' => 'Notification webhook FlexPay',
      'flexpay_check' => 'Vérification statut FlexPay',
      default => $source,
    };
  }

  /**
   * Libellé du statut de retour carte.
   *
   * @param string $status Code retour
   * @return string Libellé
   */
  public static function returnStatusLabel(string $status): string
  {
    return match ($status) {
      'cancel' => 'Annulé par l\'utilisateur',
      'decline' => 'Refusé par la banque',
      'success' => 'Succès',
      default => $status,
    };
  }

  /**
   * Libellé du statut transaction FlexPay (check API).
   *
   * @param int $status Code statut
   * @return string Libellé
   */
  public static function transactionStatusLabel(int $status): string
  {
    return match ($status) {
      0 => '0 — Payé',
      1 => '1 — Annulé',
      2 => '2 — En attente',
      -1 => 'Inconnu / non trouvé',
      default => (string) $status,
    };
  }

  /**
   * Interprétation du code FlexPay numérique.
   *
   * @param string $code Code FlexPay
   * @return string Description courte
   */
  public static function flexpayCodeLabel(string $code): string
  {
    return match ($code) {
      '0' => 'Succès',
      '1' => 'Échec / annulation',
      default => 'Erreur opérateur',
    };
  }

  /**
   * Tronque un texte long pour l'e-mail.
   *
   * @param string $text Texte source
   * @param int $max Longueur max
   * @return string Texte tronqué
   */
  private static function truncate(string $text, int $max): string
  {
    if (mb_strlen($text) <= $max) {
      return $text;
    }

    return mb_substr($text, 0, $max).'…';
  }
}
