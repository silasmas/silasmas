<?php

namespace App\Services;

use App\Mail\AcademyTemplatedMail;
use App\Models\AcademyEmailTemplate;
use App\Models\Registration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Envoie des e-mails personnalisés aux inscrits Academy à partir d'un modèle.
 */
class AcademyRegistrationMailer
{
  public function __construct(
    protected AcademyEmailTemplateRenderer $renderer
  ) {
  }

  /**
   * Envoie un e-mail basé sur un modèle à une inscription.
   *
   * @param Registration $registration Inscription cible
   * @param AcademyEmailTemplate $template Modèle choisi
   * @param bool $force Ignorer la préférence notify_email
   * @return bool true si l'e-mail a été envoyé
   */
  public function send(Registration $registration, AcademyEmailTemplate $template, bool $force = false): bool
  {
    $registration->loadMissing('student');

    $student = $registration->student;

    if ($student === null || empty($student->email)) {
      return false;
    }

    if (! $force && $registration->notify_email === false) {
      return false;
    }

    if (! $template->is_active) {
      return false;
    }

    $rendered = $this->renderer->render($template, $registration);

    try {
      Mail::to($student->email)->send(new AcademyTemplatedMail(
        $rendered['subject'],
        $rendered['body'],
        $student->firstname
      ));

      return true;
    } catch (\Throwable $exception) {
      Log::warning('Academy templated mail failed', [
        'registration_id' => $registration->id,
        'template_id' => $template->id,
        'error' => $exception->getMessage(),
      ]);

      return false;
    }
  }
}
