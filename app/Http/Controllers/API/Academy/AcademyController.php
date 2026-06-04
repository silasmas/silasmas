<?php

namespace App\Http\Controllers\API\Academy;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Academy\StoreRegistrationRequest;
use App\Http\Resources\Academy\RegistrationResource;
use App\Http\Resources\Academy\TrainingSessionResource;
use App\Models\Registration;
use App\Models\SessionPayment;
use App\Models\Student;
use App\Models\TrainingSession;
use App\Services\AcademyRegistrationNotifier;
use App\Support\ParticipantToken;
use Illuminate\Http\Request;

/**
 * API publique SDev Academy (sessions et inscriptions).
 */
class AcademyController extends BaseController
{
  /**
   * Liste les sessions ouvertes ou mises en avant.
   */
  public function sessions(Request $request)
  {
    $query = TrainingSession::query()
      ->visibleOnWeb()
      ->orderByDesc('is_featured')
      ->orderBy('start_date');

    if ($request->boolean('featured_only')) {
      $query->where('is_featured', true);
    }

    if ($request->boolean('open_only')) {
      $query->openForRegistration();
    }

    $sessions = $query->get();

    return $this->handleResponse(
      TrainingSessionResource::collection($sessions),
      'Sessions de formation trouvées'
    );
  }

  /**
   * Affiche une session par son slug.
   */
  public function showSession(string $slug)
  {
    $session = TrainingSession::where('slug', $slug)->first();

    if ($session === null) {
      return $this->handleError('Session de formation introuvable', [], 404);
    }

    if ($session->status === 'draft') {
      return $this->handleError('Session de formation introuvable', [], 404);
    }

    return $this->handleResponse(
      new TrainingSessionResource($session),
      'Session de formation trouvée'
    );
  }

  /**
   * Enregistre une inscription à une session Academy.
   */
  public function register(StoreRegistrationRequest $request)
  {
    $session = TrainingSession::where('slug', $request->training_session_slug)->firstOrFail();

    if (!$session->acceptsRegistrations()) {
      return $this->handleError(
        'Les inscriptions pour cette session sont fermées ou complètes.',
        [],
        422
      );
    }

    $existingRegistration = Registration::query()
      ->where('training_session_id', $session->id)
      ->whereHas('student', fn ($query) => $query->where('email', $request->email))
      ->first();

    if ($existingRegistration !== null && $existingRegistration->status !== 'cancelled') {
      return $this->handleError(
        'Vous êtes déjà inscrit(e) à cette session avec cette adresse e-mail.',
        [],
        422
      );
    }

    $student = Student::updateOrCreate(
      ['email' => $request->email],
      [
        'firstname' => $request->firstname,
        'lastname' => $request->lastname,
        'phone' => $request->phone,
        'city' => $request->city,
        'country' => $request->country ?? 'RDC',
        'education_level' => $request->education_level,
        'occupation' => $request->occupation,
        'marketing_opt_in' => $request->boolean('marketing_opt_in', true),
      ]
    );

    $requiresPayment = $session->isPaid();
    $initialStatus = $requiresPayment ? 'pending_payment' : 'confirmed';

    $notifyFlags = $this->resolveNotificationFlags($request, $session);
    $accessToken = ParticipantToken::generate();

    if ($existingRegistration !== null && $existingRegistration->status === 'cancelled') {
      $existingRegistration->update([
        'status' => $initialStatus,
        'motivation' => $request->motivation,
        'source' => 'website',
        'registered_at' => now(),
        'access_token' => $accessToken,
        'notify_email' => $notifyFlags['notify_email'],
        'notify_sms' => $notifyFlags['notify_sms'],
        'notify_whatsapp' => $notifyFlags['notify_whatsapp'],
        'confirmation_notified_at' => null,
      ]);
      $registration = $existingRegistration->fresh(['student', 'trainingSession']);
    } else {
      $registration = Registration::create([
        'student_id' => $student->id,
        'training_session_id' => $session->id,
        'status' => $initialStatus,
        'motivation' => $request->motivation,
        'source' => 'website',
        'registered_at' => now(),
        'access_token' => $accessToken,
        'notify_email' => $notifyFlags['notify_email'],
        'notify_sms' => $notifyFlags['notify_sms'],
        'notify_whatsapp' => $notifyFlags['notify_whatsapp'],
      ]);
      $registration->load(['student', 'trainingSession']);
    }

    if (! $requiresPayment && $initialStatus === 'confirmed') {
      app(AcademyRegistrationNotifier::class)->sendConfirmation($registration, false);
    }

    $paymentPayload = null;

    if ($requiresPayment) {
      $payment = SessionPayment::create([
        'training_session_id' => $session->id,
        'registration_id' => $registration->id,
        'student_id' => $student->id,
        'amount' => $session->registrationAmount(),
        'currency' => $session->registrationCurrency(),
        'status' => 'pending',
        'reference' => generateAcademyPaymentReference(),
      ]);

      $paymentPayload = [
        'reference' => $payment->reference,
        'amount' => (float) $payment->amount,
        'currency' => $payment->currency,
        'status' => $payment->status,
      ];
    }

    $message = $requiresPayment
      ? 'Inscription enregistrée. Procédez au paiement pour confirmer votre place.'
      : 'Inscription confirmée avec succès. Nous vous contacterons prochainement.';

    return $this->handleResponse([
      'registration' => new RegistrationResource($registration),
      'requires_payment' => $requiresPayment,
      'payment' => $paymentPayload,
      'participant_url' => ParticipantToken::frontendUrl($registration),
      'access_token' => $registration->access_token,
    ], $message, 201);
  }

  /**
   * Détermine les canaux de notification cochés par le participant.
   *
   * @return array{notify_email: bool, notify_sms: bool, notify_whatsapp: bool}
   */
  protected function resolveNotificationFlags(Request $request, TrainingSession $session): array
  {
    return [
      'notify_email' => $session->notify_by_email
        ? $request->boolean('notify_email', true)
        : false,
      'notify_sms' => ($session->notify_by_sms ?? false)
        && $request->boolean('notify_sms', false),
      'notify_whatsapp' => ($session->notify_by_whatsapp ?? false)
        && $request->boolean('notify_whatsapp', false),
    ];
  }
}
