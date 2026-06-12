<?php

namespace App\Services;

use App\Mail\AcademyRegistrationOpenMail;
use App\Models\Registration;
use App\Models\TrainingSession;
use App\Support\FrontendUrl;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Notifie les pré-inscrits lorsque les inscriptions officielles s'ouvrent.
 */
class AcademyPreRegistrationNotifier
{
  /**
   * Envoie l'e-mail d'ouverture à une pré-inscription si éligible.
   *
   * @param Registration $registration Pré-inscription
   * @param bool $forceIgnoreNotified Renvoyer même si déjà notifié
   * @return bool True si un e-mail a été envoyé
   */
  public function sendRegistrationOpen(Registration $registration, bool $forceIgnoreNotified = false): bool
  {
    $registration->loadMissing(['student', 'trainingSession']);

    if ($registration->status !== 'pre_registered') {
      return false;
    }

    if (! $forceIgnoreNotified && $registration->pre_registration_notified_at !== null) {
      return false;
    }

    $session = $registration->trainingSession;

    if ($session === null || ! $session->acceptsRegistrations()) {
      return false;
    }

    if (! ($registration->notify_email ?? true)) {
      return false;
    }

    $studentEmail = $registration->student?->email;

    if ($studentEmail === null || $studentEmail === '') {
      return false;
    }

    $registrationUrl = FrontendUrl::to('academy/'.$session->slug.'#inscription');

    try {
      Mail::to($studentEmail)->send(
        new AcademyRegistrationOpenMail($registration, $registrationUrl)
      );
    } catch (\Throwable $e) {
      Log::error('Academy pre-registration open email failed', [
        'registration_id' => $registration->id,
        'error' => $e->getMessage(),
      ]);

      return false;
    }

    $registration->update(['pre_registration_notified_at' => now()]);

    return true;
  }

  /**
   * Notifie tous les pré-inscrits d'une session qui n'ont pas encore reçu l'e-mail.
   *
   * @param TrainingSession $session Session concernée
   * @param bool $forceIgnoreNotified Renvoyer à tous
   * @return int Nombre d'e-mails envoyés
   */
  public function notifySessionPreRegistered(
    TrainingSession $session,
    bool $forceIgnoreNotified = false
  ): int {
    $query = $session->preRegisteredRegistrations()
      ->with(['student', 'trainingSession']);

    if (! $forceIgnoreNotified) {
      $query->whereNull('pre_registration_notified_at');
    }

    $sent = 0;

    foreach ($query->get() as $registration) {
      if ($this->sendRegistrationOpen($registration, $forceIgnoreNotified)) {
        $sent++;
      }
    }

    return $sent;
  }

  /**
   * Notifie automatiquement les pré-inscrits des sessions dont l'ouverture est effective.
   *
   * @return int Nombre total d'e-mails envoyés
   */
  public function notifyAllEligibleSessions(): int
  {
    $sessions = TrainingSession::query()
      ->where('pre_registration_enabled', true)
      ->where('status', 'open')
      ->whereNotNull('registration_opens_at')
      ->where('registration_opens_at', '<=', now())
      ->get();

    $total = 0;

    foreach ($sessions as $session) {
      $total += $this->notifySessionPreRegistered($session);
    }

    return $total;
  }
}
