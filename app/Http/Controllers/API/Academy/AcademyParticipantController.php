<?php

namespace App\Http\Controllers\API\Academy;

use App\Http\Controllers\API\BaseController;
use App\Http\Resources\Academy\ParticipantSpaceResource;
use App\Models\Registration;
use Illuminate\Http\Request;

/**
 * Espace participant (compte à rebours, ressources, profil).
 */
class AcademyParticipantController extends BaseController
{
  /**
   * Affiche l'espace participant via jeton d'accès.
   */
  public function show(string $token)
  {
    $registration = Registration::where('access_token', $token)
      ->with(['student', 'trainingSession'])
      ->first();

    if ($registration === null) {
      return $this->handleError('Accès participant introuvable.', [], 404);
    }

    if ($registration->status !== 'confirmed') {
      return $this->handleError(
        'Votre inscription n\'est pas encore confirmée. Finalisez le paiement si nécessaire.',
        [],
        403
      );
    }

    return $this->handleResponse(
      new ParticipantSpaceResource($registration),
      'Espace participant'
    );
  }

  /**
   * Enregistre l'acceptation de la notice de confidentialité.
   */
  public function acceptConfidentiality(string $token)
  {
    $registration = Registration::where('access_token', $token)->first();

    if ($registration === null) {
      return $this->handleError('Accès introuvable.', [], 404);
    }

    $registration->update(['confidentiality_accepted_at' => now()]);

    return $this->handleResponse(
      ['accepted_at' => $registration->confidentiality_accepted_at->format('Y-m-d H:i:s')],
      'Confidentialité acceptée'
    );
  }
}
