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
    'notify_email' => 'boolean',
    'notify_sms' => 'boolean',
    'notify_whatsapp' => 'boolean',
    'confidentiality_accepted_at' => 'datetime',
    'confirmation_notified_at' => 'datetime',
    'last_reminder_at' => 'datetime',
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

  /**
   * Paiements liés à cette inscription.
   */
  public function payments()
  {
    return $this->hasMany(SessionPayment::class);
  }

  /**
   * Dernier paiement en cours ou réussi.
   */
  public function latestPayment()
  {
    return $this->hasOne(SessionPayment::class)->latestOfMany();
  }
}
