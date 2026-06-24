<?php

namespace App\Services;

use App\Models\AcademyEmailTemplate;
use App\Models\Registration;
use App\Support\EmailBodyFormatter;
use App\Support\FrontendUrl;
use App\Support\RegistrationPaymentResumeUrl;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;

/**
 * Génère l'aperçu HTML des e-mails Academy (conception et envoi).
 */
class AcademyEmailPreviewRenderer
{
  public function __construct(
    protected AcademyEmailTemplateRenderer $templateRenderer
  ) {
  }

  /**
   * Champ Filament d'aperçu e-mail (compatible mise à jour live).
   *
   * @param string $name Clé unique du champ formulaire
   * @param callable(Get): array $resolveData Fabrique les données passées à la vue Blade
   * @return Placeholder Composant Placeholder Filament
   */
  public static function filamentPreviewField(string $name, callable $resolveData): Placeholder
  {
    return Placeholder::make($name)
      ->label('')
      ->content(function (Get $get) use ($resolveData): HtmlString {
        $data = $resolveData($get);

        return new HtmlString(
          view('filament.components.academy-email-preview', $data)->render()
        );
      })
      ->columnSpanFull();
  }

  /**
   * Données d'aperçu avec variables exemple (édition de modèle).
   *
   * @param string $subject Objet saisi
   * @param string $body Corps saisi
   * @return array{subject: string, body_html: string, payment_resume_url: string|null}
   */
  public function buildSamplePreviewData(string $subject, string $body): array
  {
    $variables = $this->sampleVariables();
    $cleanSubject = preg_replace('/\*\*(\{\{[^}]+\}\})\*\*/', '$1', $subject) ?? $subject;
    $cleanBody = preg_replace('/\*\*(\{\{[^}]+\}\})\*\*/', '$1', $body) ?? $body;
    $renderedSubject = str_replace(array_keys($variables), array_values($variables), $cleanSubject);
    $renderedBody = str_replace(array_keys($variables), array_values($variables), $cleanBody);
    $resumeUrl = $variables['{{lien_paiement}}'];

    $renderedBody = RegistrationPaymentResumeUrl::replaceLegacyInscriptionLinks(
      $renderedBody,
      $resumeUrl,
      'vibe-coding-la-nouvelle-facon-de-developper-avec-lia'
    );

    return [
      'subject' => $renderedSubject,
      'body_html' => EmailBodyFormatter::bodyToHtml($renderedBody),
      'payment_resume_url' => $resumeUrl,
      'firstname' => 'Marie',
    ];
  }

  /**
   * Données d'aperçu réelles pour une inscription cible.
   *
   * @param AcademyEmailTemplate $template Modèle choisi
   * @param Registration $registration Inscription destinataire
   * @return array{subject: string, body_html: string, payment_resume_url: string|null, firstname: string}
   */
  public function buildRegistrationPreviewData(
    AcademyEmailTemplate $template,
    Registration $registration
  ): array {
    $registration->loadMissing(['student', 'trainingSession', 'latestPayment']);
    $rendered = $this->templateRenderer->render($template, $registration);
    $paymentResumeUrl = $registration->paymentResumeUrlOrNull();
    $body = $rendered['body'];

    if ($paymentResumeUrl !== null) {
      $body = RegistrationPaymentResumeUrl::replaceLegacyInscriptionLinks(
        $body,
        $paymentResumeUrl,
        $registration->trainingSession?->slug
      );
    }

    return [
      'subject' => $rendered['subject'],
      'body_html' => EmailBodyFormatter::bodyToHtml($body),
      'payment_resume_url' => $paymentResumeUrl,
      'firstname' => $registration->student?->firstname ?? '',
    ];
  }

  /**
   * Variables exemple pour l'aperçu instantané à la conception.
   *
   * @return array<string, string>
   */
  protected function sampleVariables(): array
  {
    $slug = 'vibe-coding-la-nouvelle-facon-de-developper-avec-lia';
    $resumeUrl = FrontendUrl::to("academy/{$slug}/reprendre/exemple-jeton-apercu");

    return [
      '{{prenom}}' => 'Marie',
      '{{nom}}' => 'Kabila',
      '{{nom_complet}}' => 'Marie Kabila',
      '{{email}}' => 'marie@exemple.com',
      '{{telephone}}' => '+243 900 000 000',
      '{{ville}}' => 'Kinshasa',
      '{{pays}}' => 'RDC',
      '{{niveau_etudes}}' => 'Licence',
      '{{profession}}' => 'Développeuse',
      '{{motivation}}' => 'Apprendre le Vibe Coding avec l\'IA.',
      '{{session_titre}}' => 'VIBE CODING : développer avec l\'IA',
      '{{session_dates}}' => '29 juin 2026 — 30 juin 2026',
      '{{montant}}' => '35,00',
      '{{devise}}' => 'USD',
      '{{reference_paiement}}' => 'ACAD-EXEMPLE-001',
      '{{statut_paiement}}' => 'En attente',
      '{{lien_inscription}}' => FrontendUrl::to("academy/{$slug}#inscription"),
      '{{lien_paiement}}' => $resumeUrl,
      '{{lien_participant}}' => FrontendUrl::to('academy/espace/exemple-jeton-apercu'),
      '{{statut_inscription}}' => 'En attente de paiement',
    ];
  }
}
