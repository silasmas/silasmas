<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
    'failed_at' => 'datetime',
    'admin_notified_at' => 'datetime',
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

  /**
   * Paiements échoués ou annulés.
   *
   * @param Builder $query Requête Eloquent
   * @return Builder
   */
  public function scopeFailedOrCancelled(Builder $query): Builder
  {
    return $query->whereIn('status', ['failed', 'cancelled']);
  }

  /**
   * Libellé français du contexte d'échec.
   *
   * @param string|null $context Code technique
   * @return string Libellé affichable
   */
  public static function failureContextLabel(?string $context): string
  {
    return match ($context) {
      'mobile_init' => 'Échec initiation Mobile Money',
      'card_init' => 'Échec initiation carte bancaire',
      'card_cancel' => 'Paiement carte annulé',
      'card_decline' => 'Paiement carte refusé',
      'card_error' => 'Erreur retour carte',
      'polling_cancelled' => 'Annulation confirmée (vérification)',
      'polling_failed' => 'Échec confirmé (vérification)',
      'webhook_failed' => 'Échec webhook FlexPay',
      default => $context ?? 'Inconnu',
    };
  }
}
