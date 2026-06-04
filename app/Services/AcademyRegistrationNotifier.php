<?php

namespace App\Services;

use App\Mail\AcademyRegistrationConfirmedMail;
use App\Models\Registration;
use App\Support\ParticipantToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Envoie les notifications d'inscription (e-mail, SMS, WhatsApp).
 */
class AcademyRegistrationNotifier
{
  /**
   * Notifie le participant après confirmation (inscription ou paiement).
   *
   * @param Registration $registration Inscription avec relations chargées
   * @param bool $paymentConfirmed Message post-paiement
   * @return void
   */
  public function sendConfirmation(Registration $registration, bool $paymentConfirmed = false): void
  {
    $registration->loadMissing(['student', 'trainingSession']);

    if (empty($registration->access_token)) {
      $registration->update(['access_token' => ParticipantToken::generate()]);
      $registration->refresh();
    }

    if ($registration->confirmation_notified_at !== null && ! $paymentConfirmed) {
      return;
    }

    if ($paymentConfirmed) {
      $registration->update(['confirmation_notified_at' => null]);
    }

    $session = $registration->trainingSession;

    if ($session->notify_by_email ?? true) {
      $this->sendEmail($registration, $paymentConfirmed);
    }

    if ($registration->notify_sms && ($session->notify_by_sms ?? false)) {
      $this->sendSms($registration, $paymentConfirmed);
    }

    if ($registration->notify_whatsapp && ($session->notify_by_whatsapp ?? false)) {
      $this->sendWhatsapp($registration, $paymentConfirmed);
    }

    $registration->update(['confirmation_notified_at' => now()]);
  }

  /**
   * Rappel avant le début de la session.
   *
   * @param Registration $registration Inscription
   * @return void
   */
  public function sendReminder(Registration $registration): void
  {
    $registration->loadMissing(['student', 'trainingSession']);
    $session = $registration->trainingSession;
    $url = ParticipantToken::frontendUrl($registration);
    $title = $session->title ?? 'SDev Academy';
    $start = $session->start_date?->format('d/m/Y') ?? '';
    $body = "Rappel SDev Academy : la formation « {$title} » commence le {$start}. Votre espace : {$url}";

    if ($registration->notify_email && ($session->notify_by_email ?? true)) {
      Mail::raw($body, function ($message) use ($registration, $title) {
        $message->to($registration->student->email)
          ->subject("Rappel — {$title}");
      });
    }

    if ($registration->notify_sms && ($session->notify_by_sms ?? false)) {
      $this->dispatchExternalMessage($registration->student->phone, $body, 'sms');
    }

    if ($registration->notify_whatsapp && ($session->notify_by_whatsapp ?? false)) {
      $this->dispatchExternalMessage($registration->student->phone, $body, 'whatsapp');
    }

    $registration->update(['last_reminder_at' => now()]);
  }

  /**
   * Envoie l'e-mail de confirmation.
   */
  protected function sendEmail(Registration $registration, bool $paymentConfirmed): void
  {
    try {
      Mail::to($registration->student->email)->send(
        new AcademyRegistrationConfirmedMail($registration, $paymentConfirmed)
      );
    } catch (\Throwable $e) {
      Log::error('Academy email confirmation failed', [
        'registration_id' => $registration->id,
        'error' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Envoie un SMS via webhook optionnel.
   */
  protected function sendSms(Registration $registration, bool $paymentConfirmed): void
  {
    $url = ParticipantToken::frontendUrl($registration);
    $title = $registration->trainingSession->title ?? 'SDev Academy';
    $intro = $paymentConfirmed ? 'Paiement confirmé' : 'Inscription confirmée';

    $this->dispatchExternalMessage(
      $registration->student->phone,
      "{$intro} — {$title}. Espace : {$url}",
      'sms'
    );
  }

  /**
   * Envoie un message WhatsApp via webhook optionnel.
   */
  protected function sendWhatsapp(Registration $registration, bool $paymentConfirmed): void
  {
    $url = ParticipantToken::frontendUrl($registration);
    $title = $registration->trainingSession->title ?? 'SDev Academy';
    $intro = $paymentConfirmed ? 'Paiement confirmé' : 'Inscription confirmée';

    $this->dispatchExternalMessage(
      $registration->student->phone,
      "{$intro} — {$title}. Espace : {$url}",
      'whatsapp'
    );
  }

  /**
   * Appelle un webhook externe SMS/WhatsApp si configuré.
   *
   * @param string|null $phone Numéro
   * @param string $body Texte
   * @param string $channel sms|whatsapp
   * @return void
   */
  protected function dispatchExternalMessage(?string $phone, string $body, string $channel): void
  {
    if (empty($phone)) {
      return;
    }

    $webhook = $channel === 'sms'
      ? config('services.academy.sms_webhook_url')
      : config('services.academy.whatsapp_webhook_url');

    if (empty($webhook)) {
      Log::info("Academy {$channel} (webhook non configuré)", ['phone' => $phone, 'body' => $body]);

      return;
    }

    try {
      Http::timeout(10)->post($webhook, [
        'phone' => $phone,
        'message' => $body,
        'channel' => $channel,
      ]);
    } catch (\Throwable $e) {
      Log::error("Academy {$channel} failed", ['error' => $e->getMessage()]);
    }
  }
}
