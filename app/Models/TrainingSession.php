<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Session de formation SDev Academy (ex. édition juin 2026).
 */
class TrainingSession extends Model
{
  use HasFactory;

  protected $guarded = [];

  protected $casts = [
    'start_date' => 'date',
    'end_date' => 'date',
    'is_featured' => 'boolean',
    'max_participants' => 'integer',
  ];

  /**
   * Génère un slug à partir du titre si absent.
   */
  protected static function booted(): void
  {
    static::creating(function (TrainingSession $session) {
      if (empty($session->slug)) {
        $session->slug = Str::slug($session->title);
      }
    });
  }

  /**
   * Inscriptions liées à cette session.
   */
  public function registrations()
  {
    return $this->hasMany(Registration::class);
  }

  /**
   * Étudiants inscrits à cette session.
   */
  public function students()
  {
    return $this->belongsToMany(Student::class, 'registrations')
      ->withPivot(['status', 'motivation', 'source', 'registered_at'])
      ->withTimestamps();
  }

  /**
   * Nombre d'inscriptions confirmées ou en attente (hors annulées).
   */
  public function activeRegistrationsCount(): int
  {
    return $this->registrations()
      ->whereIn('status', ['pending', 'confirmed', 'waitlist'])
      ->count();
  }

  /**
   * Indique si la session accepte encore de nouvelles inscriptions.
   */
  public function acceptsRegistrations(): bool
  {
    if ($this->status !== 'open') {
      return false;
    }

    if ($this->max_participants === null) {
      return true;
    }

    return $this->activeRegistrationsCount() < $this->max_participants;
  }
}
