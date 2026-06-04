<?php

namespace App\Http\Resources\Site;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Représentation JSON d'un bloc de contenu site.
 */
class SiteBlockResource extends JsonResource
{
  /**
   * Transforme le bloc en tableau API.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'group' => $this->group,
      'title' => $this->title,
      'subtitle' => $this->subtitle,
      'body' => $this->body,
      'secondary_body' => $this->secondary_body,
      'icon' => $this->icon,
      'level' => $this->level,
      'image' => $this->imageUrl(),
      'sort_order' => $this->sort_order,
    ];
  }
}
