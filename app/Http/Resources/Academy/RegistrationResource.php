<?php

namespace App\Http\Resources\Academy;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Représentation JSON d'une inscription confirmée.
 */
class RegistrationResource extends JsonResource
{
  /**
   * Transforme l'inscription en tableau API.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'status' => $this->status,
      'registered_at' => $this->registered_at?->format('Y-m-d H:i:s'),
      'student' => [
        'firstname' => $this->student->firstname,
        'lastname' => $this->student->lastname,
        'email' => $this->student->email,
      ],
      'training_session' => [
        'title' => $this->trainingSession->title,
        'slug' => $this->trainingSession->slug,
        'start_date' => $this->trainingSession->start_date?->format('Y-m-d'),
        'end_date' => $this->trainingSession->end_date?->format('Y-m-d'),
      ],
    ];
  }
}
