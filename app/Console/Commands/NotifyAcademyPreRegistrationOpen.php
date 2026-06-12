<?php

namespace App\Console\Commands;

use App\Services\AcademyPreRegistrationNotifier;
use Illuminate\Console\Command;

/**
 * Envoie l'e-mail d'ouverture des inscriptions aux pré-inscrits éligibles.
 */
class NotifyAcademyPreRegistrationOpen extends Command
{
  protected $signature = 'academy:notify-pre-registration-open';

  protected $description = 'Informe par e-mail les pré-inscrits que les inscriptions sont ouvertes';

  /**
   * Exécute l'envoi automatique.
   */
  public function handle(AcademyPreRegistrationNotifier $notifier): int
  {
    $sent = $notifier->notifyAllEligibleSessions();

    $this->info("E-mails d'ouverture envoyés : {$sent}.");

    return self::SUCCESS;
  }
}
