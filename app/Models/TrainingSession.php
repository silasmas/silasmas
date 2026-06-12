<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\FrontendUrl;
use App\Support\MediaUrl;
use App\Support\MobileMoneyOperators;
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
    'pre_registration_enabled' => 'boolean',
    'registration_opens_at' => 'datetime',
    'is_free' => 'boolean',
    'price' => 'decimal:2',
    'max_participants' => 'integer',
    'notify_by_email' => 'boolean',
    'notify_by_sms' => 'boolean',
    'notify_by_whatsapp' => 'boolean',
    'payment_mobile_money_enabled' => 'boolean',
    'payment_card_enabled' => 'boolean',
    'enabled_mobile_operators' => 'array',
    'session_resources' => 'array',
    'registration_benefits' => 'array',
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
   * Paiements enregistrés pour cette session.
   */
  public function payments()
  {
    return $this->hasMany(SessionPayment::class);
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
      ->whereIn('status', ['pending', 'pending_payment', 'confirmed', 'waitlist'])
      ->count();
  }

  /**
   * Indique si la session est payante (prix défini et non gratuite).
   */
  public function isPaid(): bool
  {
    if ($this->is_free) {
      return false;
    }

    return $this->price !== null && (float) $this->price > 0;
  }

  /**
   * Montant à payer pour l'inscription (0 si gratuite).
   */
  public function registrationAmount(): float
  {
    if (! $this->isPaid()) {
      return 0.0;
    }

    return (float) $this->price;
  }

  /**
   * Devise affichée pour le paiement.
   */
  public function registrationCurrency(): string
  {
    return strtoupper($this->currency ?? 'USD');
  }

  /**
   * Opérateurs Mobile Money visibles sur le formulaire d'inscription.
   *
   * @return list<string> mpesa, airtel, orange, afrimoney
   */
  public function enabledMobileOperators(): array
  {
    $operators = MobileMoneyOperators::normalizeEnabled($this->enabled_mobile_operators);

    if ($operators !== [] || $this->enabled_mobile_operators !== null) {
      return $operators;
    }

    if ($this->payment_mobile_money_enabled === false) {
      return [];
    }

    return MobileMoneyOperators::ALL;
  }

  /**
   * Indique si le paiement Mobile Money est proposé sur le formulaire.
   */
  public function acceptsMobileMoneyPayment(): bool
  {
    return $this->enabledMobileOperators() !== [];
  }

  /**
   * Indique si le paiement par carte est proposé sur le formulaire.
   */
  public function acceptsCardPayment(): bool
  {
    return (bool) ($this->payment_card_enabled ?? true);
  }

  /**
   * Canaux de paiement activés pour cette session.
   *
   * @return list<string> mobile_money, card
   */
  public function enabledPaymentChannels(): array
  {
    $channels = [];

    if ($this->acceptsMobileMoneyPayment()) {
      $channels[] = 'mobile_money';
    }

    if ($this->acceptsCardPayment()) {
      $channels[] = 'card';
    }

    return $channels;
  }

  /**
   * Indique si la date d'ouverture des inscriptions est atteinte (ou non configurée).
   */
  public function registrationOpeningReached(): bool
  {
    if ($this->registration_opens_at === null) {
      return true;
    }

    return $this->registration_opens_at->isPast();
  }

  /**
   * Indique si la page de pré-inscription doit être affichée sur le site.
   */
  public function showsPreRegistrationPage(): bool
  {
    if (! ($this->pre_registration_enabled ?? false)) {
      return false;
    }

    if ($this->status === 'completed') {
      return false;
    }

    if ($this->status === 'open' && $this->registrationOpeningReached()) {
      return false;
    }

    return $this->registration_opens_at !== null;
  }

  /**
   * Indique si la session accepte des pré-inscriptions (intérêt avant ouverture).
   */
  public function acceptsPreRegistrations(): bool
  {
    return $this->showsPreRegistrationPage();
  }

  /**
   * URL publique de l'affiche dédiée à la pré-inscription (repli sur l'affiche session).
   *
   * @return string|null URL absolue ou null
   */
  public function preRegistrationCoverImageUrl(): ?string
  {
    $preCover = MediaUrl::publicUrl(
      $this->getRawOriginal('pre_registration_cover_image') ?? $this->pre_registration_cover_image
    );

    if ($preCover !== null) {
      return $preCover;
    }

    return $this->coverImageUrl();
  }

  /**
   * Indique si la session accepte encore de nouvelles inscriptions.
   */
  public function acceptsRegistrations(): bool
  {
    if (
      ($this->pre_registration_enabled ?? false)
      && $this->registration_opens_at !== null
      && ! $this->registrationOpeningReached()
    ) {
      return false;
    }

    if ($this->status !== 'open') {
      return false;
    }

    if ($this->max_participants === null) {
      return true;
    }

    return $this->activeRegistrationsCount() < $this->max_participants;
  }

  /**
   * Sessions visibles sur le site public (hors brouillon).
   *
   * @param Builder $query Requête Eloquent
   * @return Builder
   */
  public function scopeVisibleOnWeb(Builder $query): Builder
  {
    return $query->where(function (Builder $builder) {
      $builder->whereIn('status', ['open', 'closed', 'completed'])
        ->orWhere(function (Builder $pre) {
          $pre->where('pre_registration_enabled', true)
            ->where('status', '!=', 'completed')
            ->whereNotNull('registration_opens_at');
        });
    });
  }

  /**
   * Sessions disponibles sur le site (inscriptions ou pré-inscription active).
   *
   * @param Builder $query Requête Eloquent
   * @return Builder
   */
  public function scopeAvailableOnSite(Builder $query): Builder
  {
    return $query->visibleOnWeb()->where(function (Builder $builder) {
      $builder->where('status', 'open')
        ->orWhere(function (Builder $pre) {
          $pre->where('pre_registration_enabled', true)
            ->whereNotNull('registration_opens_at')
            ->where('status', '!=', 'completed');
        });
    });
  }

  /**
   * Sessions avec inscriptions ouvertes.
   *
   * @param Builder $query Requête Eloquent
   * @return Builder
   */
  public function scopeOpenForRegistration(Builder $query): Builder
  {
    return $query->where('status', 'open');
  }

  /**
   * Liste normalisée des avantages affichés sur la page d'inscription.
   *
   * @return list<string>
   */
  public function registrationBenefitsList(): array
  {
    $raw = $this->registration_benefits ?? [];

    if (! is_array($raw)) {
      return [];
    }

    $benefits = [];

    foreach ($raw as $item) {
      if (is_string($item)) {
        $label = trim($item);
      } elseif (is_array($item)) {
        $label = trim((string) ($item['benefit'] ?? $item['value'] ?? $item['label'] ?? reset($item) ?? ''));
      } else {
        $label = '';
      }

      if ($label !== '') {
        $benefits[] = $label;
      }
    }

    return $benefits;
  }

  /**
   * URL publique de l'affiche (storage/app/public).
   *
   * @return string|null URL absolue ou null
   */
  public function coverImageUrl(): ?string
  {
    return MediaUrl::publicUrl($this->getRawOriginal('cover_image') ?? $this->cover_image);
  }

  /**
   * URL publique de la vidéo spot (fichier ou YouTube/Vimeo).
   *
   * @return string|null URL de lecture ou d'intégration
   */
  public function spotVideoUrl(): ?string
  {
    if ($this->spot_video_type === 'youtube' || $this->spot_video_type === 'vimeo') {
      return $this->spot_video_external_url ?: null;
    }

    if ($this->spot_video_type === 'file') {
      return MediaUrl::publicUrl($this->getRawOriginal('spot_video') ?? $this->spot_video);
    }

    return null;
  }

  /**
   * URL d'intégration iframe pour YouTube / Vimeo.
   *
   * @return string|null URL embed ou null
   */
  public function spotVideoEmbedUrl(): ?string
  {
    $url = $this->spotVideoUrl();

    if ($url === null) {
      return null;
    }

    if ($this->spot_video_type === 'youtube') {
      return MediaUrl::youtubeEmbedUrl(
        $url,
        FrontendUrl::base()
      );
    }

    if ($this->spot_video_type === 'vimeo') {
      return MediaUrl::vimeoEmbedUrl($url);
    }

    return $url;
  }

  /**
   * URL publique pour ouvrir la vidéo spot (page YouTube ou fichier).
   *
   * @return string|null URL de visionnage
   */
  public function spotVideoWatchUrl(): ?string
  {
    $url = $this->spotVideoUrl();

    if ($url === null) {
      return null;
    }

    if ($this->spot_video_type === 'youtube') {
      return MediaUrl::youtubeWatchUrl($url) ?? $url;
    }

    return $url;
  }

  /**
   * Miniature de la vidéo spot (YouTube ou affiche session).
   *
   * @return string|null URL miniature
   */
  public function spotVideoThumbnailUrl(): ?string
  {
    if ($this->spot_video_type === 'youtube' && $this->spot_video_external_url) {
      return MediaUrl::youtubeThumbnailUrl($this->spot_video_external_url);
    }

    return $this->coverImageUrl();
  }
}
