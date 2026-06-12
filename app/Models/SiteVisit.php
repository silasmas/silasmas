<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Visite ou clic enregistré sur le site vitrine Next.js.
 */
class SiteVisit extends Model
{
  protected $guarded = [];

  protected $casts = [
    'visited_at' => 'datetime',
  ];

  /**
   * Filtre les pages vues uniquement.
   *
   * @param Builder $query Requête Eloquent
   * @return Builder
   */
  public function scopePageViews(Builder $query): Builder
  {
    return $query->where('event_type', 'page_view');
  }

  /**
   * Filtre les clics uniquement.
   *
   * @param Builder $query Requête Eloquent
   * @return Builder
   */
  public function scopeClicks(Builder $query): Builder
  {
    return $query->where('event_type', 'click');
  }

  /**
   * Libellé français du type d'événement.
   */
  public function eventTypeLabel(): string
  {
    return match ($this->event_type) {
      'page_view' => 'Visite',
      'click' => 'Clic',
      default => $this->event_type,
    };
  }
}
