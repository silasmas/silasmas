<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Inscription d'un étudiant à une session SDev Academy.
 */
class Registration extends Model
{
  use HasFactory;

  protected $guarded = [];

  protected $casts = [
    'registered_at' => 'datetime',
  ];

  /**
   * Étudiant inscrit.
   */
  public function student()
  {
    return $this->belongsTo(Student::class);
  }

  /**
   * Session de formation concernée.
   */
  public function trainingSession()
  {
    return $this->belongsTo(TrainingSession::class);
  }
}
