<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Bloc de contenu du site vitrine (à propos, compétences, services, etc.).
 */
class SiteBlock extends Model
{
  protected $guarded = [];

  protected $casts = [
    'level' => 'integer',
    'sort_order' => 'integer',
    'is_published' => 'boolean',
  ];

  /**
   * Blocs publiés, triés pour l'affichage.
   *
   * @param Builder $query Requête Eloquent
   * @return Builder
   */
  public function scopePublished(Builder $query): Builder
  {
    return $query
      ->where('is_published', true)
      ->orderBy('sort_order')
      ->orderBy('id');
  }

  /**
   * URL publique de l'image associée au bloc.
   *
   * @return string|null URL absolue ou null
   */
  public function imageUrl(): ?string
  {
    return MediaUrl::publicUrl($this->image);
  }
}
