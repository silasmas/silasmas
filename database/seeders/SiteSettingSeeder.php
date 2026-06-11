<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * Paramètres initiaux du site vitrine.
 */
class SiteSettingSeeder extends Seeder
{
  /**
   * Crée l'enregistrement unique de paramétrage.
   */
  public function run(): void
  {
    SiteSetting::query()->firstOrCreate(
      ['id' => 1],
      [
        'site_title' => 'Silas Développe',
        'site_tagline' => 'Solutions numériques & SDev Academy',
        'email' => 'ir-masimango@silasmas.com',
        'phone_primary' => '(+243) 827 839 232',
        'phone_secondary' => '(+243) 993 107 499',
        'address' => '01, av. des Oliviers, Limete 7ème Rue — Kinshasa, RDC',
        'footer_description' => 'SDEV offre des solutions informatiques, des accompagnements et conseils en stratégie marketing digitale et assure la couverture médiatique des évènements de tout genre.',
        'usd_to_cdf_rate' => config('site.usd_to_cdf_rate') ?? 2850,
      ]
    );
  }
}
