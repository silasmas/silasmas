<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Status;
use Illuminate\Database\Seeder;

/**
 * Importe les projets portfolio depuis les vues Blade statiques (p1–p6).
 */
class PortfolioProjectSeeder extends Seeder
{
  /**
   * Crée ou met à jour les 6 projets du portfolio.
   */
  public function run(): void
  {
    $status = Status::where('status_name', 'Actif')->first();

    $projects = [
      [
        'slug' => 'pla',
        'project_name' => 'PLA',
        'client_name' => 'Pathy Liongo & Associates',
        'category' => 'Site Web',
        'project_date' => 'Septembre, 2021',
        'web_url' => 'https://plaafricalaw.com',
        'logo_url' => 'assets/img/projets/p1/1.png',
        'gallery_urls' => [
          'assets/img/projets/p1/1.png',
          'assets/img/projets/p1/2.png',
          'assets/img/projets/p1/3.png',
        ],
        'project_description' => 'Site web multi langue et dynamique pour le cabinet PLA (Pathy Liongo & Associate), avec partie admin. Réalisé avec PHP, JavaScript, Bootstrap 5 et Laravel.',
        'sort_order' => 1,
      ],
      [
        'slug' => 'acr',
        'project_name' => 'ACR',
        'client_name' => 'Action Commune pour la République',
        'category' => 'Site Web et Mobile',
        'project_date' => 'Avril, 2023',
        'web_url' => 'https://acr-rdc.com/',
        'android_url' => 'https://play.google.com/store/search?q=acr-rdc&c=apps',
        'logo_url' => 'assets/img/projets/p2/2.png',
        'gallery_urls' => [
          'assets/img/projets/p2/1.png',
          'assets/img/projets/p2/2.png',
          'assets/img/projets/p2/3.png',
          'assets/img/projets/p2/4.png',
          'assets/img/projets/p2/5.png',
          'assets/img/projets/p2/6.png',
          'assets/img/projets/p2/7.png',
        ],
        'project_description' => 'Site et application mobile pour un parti politique en RDC : enregistrement des membres, contributions, admin. Laravel + React Native.',
        'sort_order' => 2,
      ],
      [
        'slug' => 'jp-tshienda',
        'project_name' => 'JP tshienda',
        'client_name' => 'Jean Pier Tshienda',
        'category' => 'Site Web et Mobile',
        'project_date' => 'Août, 2021',
        'web_url' => 'https://Jptshienda.cd',
        'android_url' => 'https://play.google.com/store/apps/details?id=com.jptshienda',
        'logo_url' => 'assets/img/projets/p3/1.png',
        'gallery_urls' => [
          'assets/img/projets/p3/1.png',
          'assets/img/projets/p3/2.png',
          'assets/img/projets/p3/3.png',
          'assets/img/projets/p3/4.png',
          'assets/img/projets/p3/5.png',
        ],
        'project_description' => 'Site web et application mobile multi langue avec espace admin. Laravel côté web, React Native côté mobile.',
        'sort_order' => 3,
      ],
      [
        'slug' => 'skyitup',
        'project_name' => 'Skyitup',
        'client_name' => 'Skyitup',
        'category' => 'Site Web',
        'project_date' => 'Mars, 2023',
        'web_url' => 'https://skyitupsas.com',
        'logo_url' => 'assets/img/projets/p4/1.png',
        'gallery_urls' => [
          'assets/img/projets/p4/1.png',
          'assets/img/projets/p4/2.png',
          'assets/img/projets/p4/3.png',
        ],
        'project_description' => 'Site vitrine multi langue pour une entreprise de services informatiques. PHP, JavaScript, Bootstrap 5 et Laravel.',
        'sort_order' => 4,
      ],
      [
        'slug' => 'action-damien',
        'project_name' => 'Action Damien',
        'client_name' => 'Action Damien',
        'category' => 'Site Web',
        'project_date' => 'Août, 2022',
        'web_url' => 'https://actiondamienrdcongo.org',
        'logo_url' => 'assets/img/projets/p5/1.png',
        'gallery_urls' => [
          'assets/img/projets/p5/1.png',
          'assets/img/projets/p5/2.png',
          'assets/img/projets/p5/3.png',
        ],
        'project_description' => 'Site dynamique multi langue pour un organisme international, avec espace admin. Laravel, Bootstrap 5.',
        'sort_order' => 5,
      ],
      [
        'slug' => 'groupe-gael',
        'project_name' => 'Groupe Adorons l\'éternel',
        'client_name' => 'Groupe Adorons l\'éternel',
        'category' => 'Site Web',
        'project_date' => 'Septembre, 2023',
        'web_url' => 'https://groupegael.com',
        'logo_url' => 'assets/img/projets/p6/1.png',
        'gallery_urls' => [
          'assets/img/projets/p6/1.png',
          'assets/img/projets/p6/2.png',
          'assets/img/projets/p6/3.png',
        ],
        'project_description' => 'Site vitrine statique pour le groupe GAEL. PHP, JavaScript, Bootstrap 5 et Laravel.',
        'sort_order' => 6,
      ],
    ];

    foreach ($projects as $data) {
      Project::updateOrCreate(
        ['slug' => $data['slug']],
        array_merge($data, [
          'status_id' => $status?->id,
          'is_published' => true,
        ])
      );
    }
  }
}
