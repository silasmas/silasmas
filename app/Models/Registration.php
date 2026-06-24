<?php

namespace App\Models;

use App\Support\ParticipantToken;
use App\Support\RegistrationPaymentResumeUrl;
use Illuminate\Database\Eloquent\Builder;
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
    'pre_registration_notified_at' => 'datetime',
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

  /**
   * Indique si l'inscription doit encore finaliser un paiement (session payante).
   */
  public function needsPaymentCompletion(): bool
  {
    return $this->requiresPaymentResumeLink();
  }

  /**
   * Indique si un lien de reprise paiement doit être proposé (e-mail, aperçu, envoi).
   */
  public function requiresPaymentResumeLink(): bool
  {
    if ($this->hasPaidPayment()) {
      return false;
    }

    if (in_array($this->status, ['cancelled', 'pre_registered'], true)) {
      return false;
    }

    $this->loadMissing('trainingSession');
    $session = $this->trainingSession;

    if ($session === null || empty($session->slug)) {
      return false;
    }

    if (in_array($this->status, ['pending_payment', 'pending'], true)) {
      return true;
    }

    $hasPrice = $session->price !== null && (float) $session->price > 0;

    return $hasPrice || $session->isPaid();
  }

  /**
   * URL de reprise paiement ou null si non applicable.
   *
   * @return string|null Lien frontend /reprendre/{jeton}
   */
  public function paymentResumeUrlOrNull(): ?string
  {
    if (! $this->requiresPaymentResumeLink()) {
      return null;
    }

    return RegistrationPaymentResumeUrl::frontendUrl($this);
  }

  /**
   * Indique si l'inscription peut reprendre au paiement via lien.
   */
  public function canResumePayment(): bool
  {
    return $this->needsPaymentCompletion();
  }

  /**
   * Inscriptions dont le paiement n'est pas finalisé.
   *
   * @param Builder $query Requête Eloquent
   * @return Builder
   */
  public function scopePaymentIncomplete(Builder $query): Builder
  {
    return $query
      ->whereNotIn('status', ['cancelled', 'pre_registered'])
      ->whereHas('trainingSession', function (Builder $sessionQuery) {
        $sessionQuery->where(function (Builder $paidQuery) {
          $paidQuery
            ->where(function (Builder $pricedQuery) {
              $pricedQuery->whereNotNull('price')->where('price', '>', 0);
            })
            ->orWhere(function (Builder $legacyPaidQuery) {
              $legacyPaidQuery->where('is_free', false)->where('price', '>', 0);
            });
        });
      })
      ->whereDoesntHave('payments', function (Builder $paymentQuery) {
        $paymentQuery->where('status', 'paid');
      });
  }

  /**
   * Indique si un paiement validé existe pour cette inscription.
   */
  public function hasPaidPayment(): bool
  {
    return $this->payments()->where('status', 'paid')->exists();
  }

  /**
   * Inscription confirmée (statut ou paiement effectué).
   */
  public function isFullyConfirmed(): bool
  {
    if ($this->status === 'confirmed') {
      return true;
    }

    return $this->hasPaidPayment();
  }

  /**
   * Garantit un jeton d'accès à l'espace participant.
   */
  public function ensureAccessToken(): string
  {
    if (empty($this->access_token)) {
      $this->update(['access_token' => ParticipantToken::generate()]);
      $this->refresh();
    }

    return (string) $this->access_token;
  }

  /**
   * Retourne un paiement en attente ou en crée un nouveau pour la session.
   */
  public function resolveOpenPayment(TrainingSession $session): SessionPayment
  {
    $openPayment = $this->payments()
      ->whereIn('status', ['pending', 'processing'])
      ->latest()
      ->first();

    if ($openPayment !== null) {
      return $openPayment;
    }

    return SessionPayment::create([
      'training_session_id' => $session->id,
      'registration_id' => $this->id,
      'student_id' => $this->student_id,
      'amount' => $session->registrationAmount(),
      'currency' => $session->registrationCurrency(),
      'status' => 'pending',
      'reference' => generateAcademyPaymentReference(),
    ]);
  }
}
