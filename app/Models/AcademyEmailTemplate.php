<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Modèle d'e-mail Academy avec variables dynamiques ({{prenom}}, {{session_titre}}, etc.).
 */
class AcademyEmailTemplate extends Model
{
  protected $guarded = [];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  /**
   * Génère un slug unique à partir du nom si absent.
   */
  protected static function booted(): void
  {
    static::creating(function (AcademyEmailTemplate $template) {
      if (empty($template->slug)) {
        $base = Str::slug($template->name);
        $slug = $base;
        $index = 1;

        while (static::where('slug', $slug)->exists()) {
          $slug = "{$base}-{$index}";
          $index++;
        }

        $template->slug = $slug;
      }
    });
  }

  /**
   * Modèles actifs uniquement.
   *
   * @param Builder $query Requête Eloquent
   * @return Builder
   */
  public function scopeActive(Builder $query): Builder
  {
    return $query->where('is_active', true);
  }
}
