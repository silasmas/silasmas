<?php

namespace App\Services;

use App\Models\AcademyEmailTemplate;
use App\Models\Registration;
use App\Support\FrontendUrl;
use App\Support\ParticipantToken;
use App\Support\RegistrationPaymentResumeUrl;

/**
 * Remplace les variables dynamiques dans les modèles d'e-mails Academy.
 */
class AcademyEmailTemplateRenderer
{
  /**
   * Liste des variables disponibles pour l'aide admin.
   *
   * @return array<string, string> Clé => description
   */
  public function availableVariables(): array
  {
    return [
      '{{prenom}}' => 'Prénom du participant',
      '{{nom}}' => 'Nom du participant',
      '{{nom_complet}}' => 'Nom complet',
      '{{email}}' => 'Adresse e-mail',
      '{{telephone}}' => 'Téléphone',
      '{{ville}}' => 'Ville',
      '{{pays}}' => 'Pays',
      '{{niveau_etudes}}' => 'Niveau d\'études',
      '{{profession}}' => 'Profession / occupation',
      '{{motivation}}' => 'Motivation saisie à l\'inscription',
      '{{session_titre}}' => 'Titre de la session',
      '{{session_dates}}' => 'Dates de la session',
      '{{montant}}' => 'Montant à payer',
      '{{devise}}' => 'Devise du paiement',
      '{{reference_paiement}}' => 'Référence du dernier paiement',
      '{{statut_paiement}}' => 'Statut du dernier paiement',
      '{{lien_inscription}}' => 'Lien inscription (reprise paiement automatique si non payé)',
      '{{lien_paiement}}' => 'Lien direct vers l\'étape paiement (formulaire prérempli)',
      '{{lien_participant}}' => 'Lien espace participant',
      '{{statut_inscription}}' => 'Statut de l\'inscription',
    ];
  }

  /**
   * Rend le sujet et le corps d'un modèle pour une inscription.
   *
   * @param AcademyEmailTemplate $template Modèle choisi
   * @param Registration $registration Inscription cible
   * @return array{subject: string, body: string}
   */
  public function render(AcademyEmailTemplate $template, Registration $registration): array
  {
    $registration->loadMissing(['student', 'trainingSession', 'latestPayment']);
    $variables = $this->buildVariables($registration);

    $subject = $this->replaceVariables(
      $this->stripMarkdownAroundVariables($template->subject),
      $variables
    );
    $body = $this->replaceVariables(
      $this->stripMarkdownAroundVariables($template->body),
      $variables
    );
    $body = $this->normalizePaymentLinks($body, $registration);

    return [
      'subject' => $subject,
      'body' => $body,
    ];
  }

  /**
   * Retire le gras Markdown autour des variables (ex. **{{lien_paiement}}**).
   */
  protected function stripMarkdownAroundVariables(string $text): string
  {
    $cleaned = preg_replace('/\*\*(\{\{[^}]+\}\})\*\*/', '$1', $text);

    return $cleaned ?? $text;
  }

  /**
   * Remplace les liens d'inscription génériques par le lien de reprise paiement si pertinent.
   */
  protected function normalizePaymentLinks(string $body, Registration $registration): string
  {
    if (! $registration->needsPaymentCompletion()) {
      return $body;
    }

    $session = $registration->trainingSession;

    if ($session === null || empty($session->slug)) {
      return $body;
    }

    $resumeUrl = RegistrationPaymentResumeUrl::frontendUrl($registration);
    $slug = preg_quote($session->slug, '~');
    $baseHost = preg_quote(parse_url(FrontendUrl::base(), PHP_URL_HOST) ?? 'silasmas.com', '~');

    $patterns = [
      '~https?://(?:www\.)?'.$baseHost.'/academy/'.$slug.'/?(?:#inscription)?\*{0,2}~i',
      '~https?://(?:www\.)?silasmas\.com/academy/'.$slug.'/?(?:#inscription)?\*{0,2}~i',
    ];

    foreach ($patterns as $pattern) {
      $body = preg_replace($pattern, $resumeUrl, $body) ?? $body;
    }

    $wrongLinks = [
      FrontendUrl::to("academy/{$session->slug}#inscription"),
      FrontendUrl::to("academy/{$session->slug}#inscription**"),
      FrontendUrl::to("academy/{$session->slug}"),
      rtrim(FrontendUrl::to("academy/{$session->slug}"), '/').'#inscription',
    ];

    foreach ($wrongLinks as $wrongLink) {
      $body = str_replace($wrongLink, $resumeUrl, $body);
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

  /**
   * Construit le tableau de variables pour une inscription.
   *
   * @param Registration $registration Inscription
   * @return array<string, string>
   */
  protected function buildVariables(Registration $registration): array
  {
    $student = $registration->student;
    $session = $registration->trainingSession;
    $payment = $registration->latestPayment;

    $startLabel = $session?->start_date?->locale('fr')->translatedFormat('j F Y') ?? '';
    $endLabel = $session?->end_date?->locale('fr')->translatedFormat('j F Y') ?? '';
    $sessionDates = trim($startLabel.($endLabel !== '' ? " — {$endLabel}" : ''));

    $registration->ensureAccessToken();

    $resumeUrl = $registration->needsPaymentCompletion()
      ? RegistrationPaymentResumeUrl::frontendUrl($registration)
      : null;
    $inscriptionUrl = $session?->slug
      ? FrontendUrl::to("academy/{$session->slug}#inscription")
      : FrontendUrl::to('academy');
    $paymentLink = $resumeUrl ?? $inscriptionUrl;

    return [
      '{{prenom}}' => $student?->firstname ?? '',
      '{{nom}}' => $student?->lastname ?? '',
      '{{nom_complet}}' => $student?->full_name ?? '',
      '{{email}}' => $student?->email ?? '',
      '{{telephone}}' => $student?->phone ?? '',
      '{{ville}}' => $student?->city ?? '',
      '{{pays}}' => $student?->country ?? '',
      '{{niveau_etudes}}' => $student?->education_level ?? '',
      '{{profession}}' => $student?->occupation ?? '',
      '{{motivation}}' => $registration->motivation ?? '',
      '{{session_titre}}' => $session?->title ?? '',
      '{{session_dates}}' => $sessionDates,
      '{{montant}}' => $payment?->amount !== null
        ? number_format((float) $payment->amount, 2, ',', ' ')
        : ($session?->price !== null ? number_format((float) $session->price, 2, ',', ' ') : ''),
      '{{devise}}' => $payment?->currency ?? $session?->currency ?? 'USD',
      '{{reference_paiement}}' => $payment?->reference ?? '',
      '{{statut_paiement}}' => $this->paymentStatusLabel($payment?->status),
      '{{lien_inscription}}' => $paymentLink,
      '{{lien_paiement}}' => $paymentLink,
      '{{lien_participant}}' => ParticipantToken::frontendUrl($registration),
      '{{statut_inscription}}' => $this->registrationStatusLabel($registration->status),
    ];
  }

  /**
   * Remplace les variables dans un texte (syntaxe {{cle}}).
   *
   * @param string $text Texte source
   * @param array<string, string> $variables Paires clé/valeur
   * @return string Texte rendu
   */
  protected function replaceVariables(string $text, array $variables): string
  {
    return str_replace(array_keys($variables), array_values($variables), $text);
  }

  /**
   * Libellé français du statut d'inscription.
   *
   * @param string|null $status Code statut
   * @return string Libellé
   */
  protected function registrationStatusLabel(?string $status): string
  {
    return match ($status) {
      'pending' => 'En attente',
      'pending_payment' => 'En attente de paiement',
      'confirmed' => 'Confirmée',
      'waitlist' => 'Liste d\'attente',
      'pre_registered' => 'Pré-inscrit',
      'cancelled' => 'Annulée',
      default => $status ?? '',
    };
  }

  /**
   * Libellé français du statut de paiement.
   *
   * @param string|null $status Code statut
   * @return string Libellé
   */
  protected function paymentStatusLabel(?string $status): string
  {
    return match ($status) {
      'pending' => 'En attente',
      'processing' => 'En cours',
      'paid' => 'Payé',
      'failed' => 'Échoué',
      'refunded' => 'Remboursé',
      'cancelled' => 'Annulé',
      default => $status ?? 'Non initié',
    };
  }
}
