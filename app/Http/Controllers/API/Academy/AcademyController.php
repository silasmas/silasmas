<?php

namespace App\Http\Controllers\API\Academy;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Academy\StoreRegistrationRequest;
use App\Http\Resources\Academy\RegistrationResource;
use App\Http\Resources\Academy\TrainingSessionResource;
use App\Models\Registration;
use App\Models\Student;
use App\Models\TrainingSession;
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
      ->whereIn('status', ['open', 'closed', 'completed'])
      ->orderByDesc('is_featured')
      ->orderBy('start_date');

    if ($request->boolean('featured_only')) {
      $query->where('is_featured', true);
    }

    if ($request->boolean('open_only')) {
      $query->where('status', 'open');
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

    if ($existingRegistration !== null && $existingRegistration->status === 'cancelled') {
      $existingRegistration->update([
        'status' => 'pending',
        'motivation' => $request->motivation,
        'source' => 'website',
        'registered_at' => now(),
      ]);
      $registration = $existingRegistration->fresh(['student', 'trainingSession']);
    } else {
      $registration = Registration::create([
        'student_id' => $student->id,
        'training_session_id' => $session->id,
        'status' => 'pending',
        'motivation' => $request->motivation,
        'source' => 'website',
        'registered_at' => now(),
      ]);
      $registration->load(['student', 'trainingSession']);
    }

    return $this->handleResponse(
      new RegistrationResource($registration),
      'Inscription enregistrée avec succès. Nous vous contacterons prochainement.',
      201
    );
  }
}
