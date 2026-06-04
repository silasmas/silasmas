<?php

namespace App\Console\Commands;

use App\Models\Registration;
use App\Services\AcademyRegistrationNotifier;
use Illuminate\Console\Command;

/**
 * Envoie les rappels aux participants (veille du début de session).
 */
class SendAcademyReminders extends Command
{
  protected $signature = 'academy:send-reminders';

  protected $description = 'Rappels e-mail / SMS / WhatsApp pour les formations qui commencent demain';

  /**
   * Exécute l'envoi des rappels.
   */
  public function handle(AcademyRegistrationNotifier $notifier): int
  {
    $targetDate = now()->addDay()->toDateString();

    $registrations = Registration::query()
      ->where('status', 'confirmed')
      ->whereHas('trainingSession', fn ($q) => $q->whereDate('start_date', $targetDate))
      ->where(function ($q) {
        $q->where('notify_email', true)
          ->orWhere('notify_sms', true)
          ->orWhere('notify_whatsapp', true);
      })
      ->where(function ($q) {
        $q->whereNull('last_reminder_at')
          ->orWhereDate('last_reminder_at', '<', now()->toDateString());
      })
      ->with(['student', 'trainingSession'])
      ->get();

    foreach ($registrations as $registration) {
      $notifier->sendReminder($registration);
      $this->info("Rappel envoyé — inscription #{$registration->id}");
    }

    $this->info("Total : {$registrations->count()} rappel(s).");

    return self::SUCCESS;
  }
}
