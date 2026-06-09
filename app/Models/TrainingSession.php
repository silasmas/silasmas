<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Support\FrontendUrl;
use App\Support\MediaUrl;
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
    'is_free' => 'boolean',
    'price' => 'decimal:2',
    'max_participants' => 'integer',
    'notify_by_email' => 'boolean',
    'notify_by_sms' => 'boolean',
    'notify_by_whatsapp' => 'boolean',
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

  /**
   * Sessions visibles sur le site public (hors brouillon).
   *
   * @param Builder $query Requête Eloquent
   * @return Builder
   */
  public function scopeVisibleOnWeb(Builder $query): Builder
  {
    return $query->whereIn('status', ['open', 'closed', 'completed']);
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
