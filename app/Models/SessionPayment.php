<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Paiement lié à une session Academy (inscription ou étudiant).
 */
class SessionPayment extends Model
{
  protected $guarded = [];

  protected $casts = [
    'amount' => 'decimal:2',
    'paid_at' => 'datetime',
  ];

  /**
   * Session de formation concernée.
   */
  public function trainingSession(): BelongsTo
  {
    return $this->belongsTo(TrainingSession::class);
  }

  /**
   * Inscription associée (optionnel).
   */
  public function registration(): BelongsTo
  {
    return $this->belongsTo(Registration::class);
  }

  /**
   * Étudiant payeur (optionnel).
   */
  public function student(): BelongsTo
  {
    return $this->belongsTo(Student::class);
  }
}
