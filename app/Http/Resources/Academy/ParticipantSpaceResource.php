<?php

namespace App\Http\Resources\Academy;

use App\Support\ParticipantToken;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Données de l'espace participant (compte à rebours, droits, ressources).
 */
class ParticipantSpaceResource extends JsonResource
{
  /**
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $session = $this->trainingSession;
    $student = $this->student;

    return [
      'registration' => [
        'id' => $this->id,
        'status' => $this->status,
        'registered_at' => $this->registered_at?->format('Y-m-d H:i:s'),
        'is_confirmed' => $this->status === 'confirmed',
        'confidentiality_accepted' => $this->confidentiality_accepted_at !== null,
      ],
      'student' => [
        'firstname' => $student->firstname,
        'lastname' => $student->lastname,
        'email' => $student->email,
        'phone' => $student->phone,
        'city' => $student->city,
        'country' => $student->country,
        'education_level' => $student->education_level,
      ],
      'session' => [
        'title' => $session->title,
        'slug' => $session->slug,
        'subtitle' => $session->subtitle,
        'start_date' => $session->start_date?->format('Y-m-d'),
        'end_date' => $session->end_date?->format('Y-m-d'),
        'format' => $session->format,
        'participant_benefits' => $session->participant_benefits,
        'confidentiality_notice' => $session->confidentiality_notice,
        'resources' => $session->session_resources ?? [],
      ],
      'participant_url' => ParticipantToken::frontendUrl($this->resource),
      'countdown_target' => $session->start_date?->format('Y-m-d').'T08:00:00',
    ];
  }
}
