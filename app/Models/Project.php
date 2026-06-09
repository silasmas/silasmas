<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Projet portfolio SDEV (site vitrine + étude de cas).
 */
class Project extends Model
{
  use HasFactory;

  protected $guarded = [];

  protected $casts = [
    'gallery_urls' => 'array',
    'tags' => 'array',
    'metrics' => 'array',
    'is_published' => 'boolean',
    'sort_order' => 'integer',
  ];

  /**
   * Génère automatiquement le slug à partir du nom si absent.
   */
  protected static function booted(): void
  {
    static::saving(function (Project $project): void {
      if (filled($project->slug)) {
        return;
      }

      if (! filled($project->project_name)) {
        return;
      }

      $baseSlug = Str::slug($project->project_name);
      $slug = $baseSlug;
      $suffix = 1;

      while (
        static::query()
          ->where('slug', $slug)
          ->when($project->exists, fn ($query) => $query->where('id', '!=', $project->id))
          ->exists()
      ) {
        $slug = "{$baseSlug}-{$suffix}";
        $suffix++;
      }

      $project->slug = $slug;
    });
  }

  /**
   * ONE-TO-MANY — statut du projet.
   */
  public function status()
  {
    return $this->belongsTo(Status::class);
  }

  /**
   * ONE-TO-MANY — responsable du projet.
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
