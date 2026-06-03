<?php

namespace App\Http\Resources\Academy;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Représentation JSON d'une session de formation.
 */
class TrainingSessionResource extends JsonResource
{
  /**
   * Transforme la session en tableau API.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'title' => $this->title,
      'slug' => $this->slug,
      'subtitle' => $this->subtitle,
      'description' => $this->description,
      'program' => $this->program,
      'start_date' => $this->start_date?->format('Y-m-d'),
      'end_date' => $this->end_date?->format('Y-m-d'),
      'format' => $this->format,
      'status' => $this->status,
      'max_participants' => $this->max_participants,
      'is_featured' => $this->is_featured,
      'cover_image' => $this->cover_image,
      'accepts_registrations' => $this->acceptsRegistrations(),
      'active_registrations_count' => $this->when(
        $request->routeIs('academy.sessions.show'),
        fn () => $this->activeRegistrationsCount()
      ),
    ];
  }
}
