<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Étudiant / participant SDev Academy (base CRM formations).
 */
class Student extends Model
{
  use HasFactory;

  protected $guarded = [];

  protected $casts = [
    'marketing_opt_in' => 'boolean',
  ];

  /**
   * Nom complet pour l'affichage admin.
   */
  public function getFullNameAttribute(): string
  {
    return trim("{$this->firstname} {$this->lastname}");
  }

  /**
   * Inscriptions aux sessions de formation.
   */
  public function registrations()
  {
    return $this->hasMany(Registration::class);
  }

  /**
   * Sessions auxquelles l'étudiant est inscrit.
   */
  public function trainingSessions()
  {
    return $this->belongsToMany(TrainingSession::class, 'registrations')
      ->withPivot(['status', 'motivation', 'source', 'registered_at'])
      ->withTimestamps();
  }
}
