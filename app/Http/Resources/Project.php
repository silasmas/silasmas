<?php

namespace App\Http\Resources;

use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Représentation JSON d'un projet portfolio.
 */
class Project extends JsonResource
{
  /**
   * Transforme le projet en tableau API.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $logoUrl = MediaUrl::publicUrl($this->logo_url);
    $gallery = collect($this->gallery_urls ?? [])
      ->map(fn ($item) => MediaUrl::publicUrl($item))
      ->filter()
      ->values()
      ->all();

    return [
      'id' => $this->id,
      'project_name' => $this->project_name,
      'slug' => $this->slug,
      'project_description' => $this->project_description,
      'client_name' => $this->client_name,
      'category' => $this->category,
      'project_date' => $this->project_date,
      'web_url' => $this->web_url,
      'android_url' => $this->android_url,
      'ios_url' => $this->ios_url,
      'logo_url' => $logoUrl,
      'gallery_urls' => $gallery,
      'sort_order' => $this->sort_order,
      'status' => $this->status ? Status::make($this->status) : null,
      'user' => $this->user ? User::make($this->user) : null,
      'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
      'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
    ];
  }
}
