<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Model;

/**
 * Paramètres globaux du site vitrine (enregistrement unique).
 */
class SiteSetting extends Model
{
  protected $guarded = [];

  protected $casts = [
    'usd_to_cdf_rate' => 'decimal:2',
  ];

  /**
   * Retourne l'enregistrement unique de paramétrage (crée les valeurs par défaut si absent).
   *
   * @return self Instance singleton
   */
  public static function instance(): self
  {
    $settings = self::query()->first();

    if ($settings !== null) {
      return $settings;
    }

    return self::query()->create([
      'site_title' => 'Silas Développe',
      'site_tagline' => 'Solutions numériques & SDev Academy',
      'email' => 'ir-masimango@silasmas.com',
      'phone_primary' => '(+243) 827 839 232',
      'phone_secondary' => '(+243) 993 107 499',
      'address' => '01, av. des Oliviers, Limete 7ème Rue — Kinshasa, RDC',
      'footer_description' => 'SDEV offre des solutions informatiques, des accompagnements et conseils en stratégie marketing digitale.',
    ]);
  }

  /**
   * URL publique du logo.
   *
   * @return string|null URL absolue ou null
   */
  public function logoUrl(): ?string
  {
    return MediaUrl::publicUrl($this->logo);
  }

  /**
   * URL publique du favicon.
   *
   * @return string|null URL absolue ou null
   */
  public function faviconUrl(): ?string
  {
    return MediaUrl::publicUrl($this->favicon);
  }

  /**
   * Représentation API pour le front Next.js.
   *
   * @return array<string, mixed>
   */
  public function toApiArray(): array
  {
    return [
      'site_title' => $this->site_title,
      'site_tagline' => $this->site_tagline,
      'logo_url' => $this->logoUrl(),
      'favicon_url' => $this->faviconUrl(),
      'email' => $this->email,
      'phone_primary' => $this->phone_primary,
      'phone_secondary' => $this->phone_secondary,
      'address' => $this->address,
      'footer_description' => $this->footer_description,
      'usd_to_cdf_rate' => $this->usd_to_cdf_rate !== null
        ? (float) $this->usd_to_cdf_rate
        : null,
    ];
  }
}
