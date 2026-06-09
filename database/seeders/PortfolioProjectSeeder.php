<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Status;
use Illuminate\Database\Seeder;

/**
 * Importe les projets portfolio avec études de cas complètes.
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
        'project_name' => 'PLA — Pathy Liongo & Associés',
        'client_name' => 'Cabinet juridique & finance',
        'category' => 'Site Web',
        'project_date' => '2021',
        'web_url' => 'https://plaafricalaw.com',
        'logo_url' => 'assets/img/projets/p1/1.png',
        'gallery_urls' => [
          'assets/img/projets/p1/1.png',
          'assets/img/projets/p1/2.png',
          'assets/img/projets/p1/3.png',
        ],
        'project_description' =>
          'Site bilingue dynamique pour un cabinet panafricain de conseil juridique et financier.',
        'context' =>
          'Le cabinet PLA accompagne investisseurs et institutions à travers l\'Afrique centrale. '
          . 'Il avait besoin d\'un site capable de refléter son sérieux et sa portée internationale.',
        'challenge' =>
          'Concilier la rigueur d\'une marque juridique avec une expérience web contemporaine, '
          . 'multilingue et facilement administrable par les équipes.',
        'outcome' =>
          'Une plateforme bilingue (FR/EN), un back-office Laravel et une architecture modulaire '
          . 'qui sert aujourd\'hui de socle à toutes les communications du cabinet.',
        'tags' => ['Laravel', 'PHP', 'Bootstrap', 'JavaScript', 'i18n'],
        'metrics' => [
          ['label' => 'Pages multilingues', 'value' => '40+'],
          ['label' => 'Langues', 'value' => 'FR / EN'],
          ['label' => 'Admin', 'value' => 'Laravel'],
        ],
        'sort_order' => 1,
      ],
      [
        'slug' => 'acr',
        'project_name' => 'ACR — Application mobile',
        'client_name' => 'Action Commune pour la République',
        'category' => 'Application',
        'project_date' => '2023',
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
        'project_description' =>
          'Site et application mobile pour l\'enregistrement des membres et le suivi terrain.',
        'context' =>
          'Un mouvement politique avait besoin d\'un outil fiable pour enregistrer et suivre '
          . 'des dizaines de milliers de membres sur le territoire national.',
        'challenge' =>
          'Travailler en zones à connectivité limitée, avec une UX simple pour des opérateurs '
          . 'non-techniques et une supervision en temps réel depuis le siège.',
        'outcome' =>
          'Une app multilangue avec synchronisation, dashboard administrateur Laravel '
          . 'et site vitrine qui a réduit les délais de traitement de plusieurs semaines.',
        'tags' => ['React Native', 'Laravel', 'Realtime', 'Mobile'],
        'metrics' => [
          ['label' => 'Membres', 'value' => '60k+'],
          ['label' => 'Provinces', 'value' => '12'],
          ['label' => 'Stack', 'value' => 'RN + Laravel'],
        ],
        'sort_order' => 2,
      ],
      [
        'slug' => 'jp-tshienda',
        'project_name' => 'Fondation Jean-Pierre Tshienda',
        'client_name' => 'Fondation',
        'category' => 'Plateforme',
        'project_date' => '2021',
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
        'project_description' =>
          'Site et application mobile pour rassembler la communauté autour d\'une figure publique.',
        'context' =>
          'Donner à la fondation une vitrine moderne et un canal de mobilisation au-delà des réseaux sociaux.',
        'challenge' =>
          'Gérer un large volume de contenus — articles, événements, médias — '
          . 'et offrir une expérience cohérente sur web et mobile.',
        'outcome' =>
          'Un écosystème éditorial unifié Laravel + React Native, avec espace admin '
          . 'et application mobile multilingue.',
        'tags' => ['Laravel', 'React Native', 'Mobile', 'i18n'],
        'metrics' => [
          ['label' => 'Plateformes', 'value' => 'Web + Mobile'],
          ['label' => 'Langues', 'value' => 'Multi'],
          ['label' => 'Admin', 'value' => 'Laravel'],
        ],
        'sort_order' => 3,
      ],
      [
        'slug' => 'skyitup',
        'project_name' => 'Skyitup',
        'client_name' => 'Skyitup SAS',
        'category' => 'Site Web',
        'project_date' => '2023',
        'web_url' => 'https://skyitupsas.com',
        'logo_url' => 'assets/img/projets/p4/1.png',
        'gallery_urls' => [
          'assets/img/projets/p4/1.png',
          'assets/img/projets/p4/2.png',
          'assets/img/projets/p4/3.png',
        ],
        'project_description' =>
          'Site vitrine multilingue pour une entreprise de services informatiques.',
        'context' =>
          'Skyitup avait besoin d\'une présence en ligne professionnelle pour présenter '
          . 'ses services et rassurer ses clients entreprises.',
        'challenge' =>
          'Créer un site clair, multilingue et facile à mettre à jour sans équipe technique interne.',
        'outcome' =>
          'Un site vitrine Laravel avec back-office, design responsive et contenu administrable.',
        'tags' => ['Laravel', 'PHP', 'Bootstrap', 'i18n'],
        'metrics' => [
          ['label' => 'Langues', 'value' => 'Multi'],
          ['label' => 'Type', 'value' => 'Site vitrine'],
          ['label' => 'CMS', 'value' => 'Laravel'],
        ],
        'sort_order' => 4,
      ],
      [
        'slug' => 'action-damien',
        'project_name' => 'Action Damien — RDC',
        'client_name' => 'ONG internationale',
        'category' => 'Site Web',
        'project_date' => '2022',
        'web_url' => 'https://actiondamienrdcongo.org',
        'logo_url' => 'assets/img/projets/p5/1.png',
        'gallery_urls' => [
          'assets/img/projets/p5/1.png',
          'assets/img/projets/p5/2.png',
          'assets/img/projets/p5/3.png',
        ],
        'project_description' =>
          'Site institutionnel multilingue pour un organisme de santé publique.',
        'context' =>
          'L\'ONG voulait moderniser sa présence en ligne et donner plus de visibilité à ses programmes terrain.',
        'challenge' =>
          'Fédérer des contenus en plusieurs langues, mettre en avant l\'impact, '
          . 'tout en restant simple à administrer.',
        'outcome' =>
          'Un site éditorial clair, multilingue, avec un back-office Laravel adapté aux équipes communication.',
        'tags' => ['Laravel', 'i18n', 'CMS', 'Bootstrap'],
        'metrics' => [
          ['label' => 'Langues', 'value' => '3'],
          ['label' => 'Type', 'value' => 'Institutionnel'],
          ['label' => 'Admin', 'value' => 'Laravel'],
        ],
        'sort_order' => 5,
      ],
      [
        'slug' => 'groupe-gael',
        'project_name' => 'Groupe Adorons l\'Éternel',
        'client_name' => 'Communauté musicale',
        'category' => 'Application',
        'project_date' => '2023',
        'web_url' => 'https://groupegael.com',
        'logo_url' => 'assets/img/projets/p6/1.png',
        'gallery_urls' => [
          'assets/img/projets/p6/1.png',
          'assets/img/projets/p6/2.png',
          'assets/img/projets/p6/3.png',
        ],
        'project_description' =>
          'Site et plateforme pour un collectif musical de référence en Afrique francophone.',
        'context' =>
          'Donner au collectif un canal direct vers sa communauté, indépendamment des plateformes tierces.',
        'challenge' =>
          'Combiner contenu éditorial, médiathèque et présence en ligne dans une expérience cohérente.',
        'outcome' =>
          'Une plateforme unifiée Laravel qui consolide l\'identité du groupe et sa visibilité digitale.',
        'tags' => ['Laravel', 'PHP', 'Bootstrap', 'Média'],
        'metrics' => [
          ['label' => 'Communauté', 'value' => '50k+'],
          ['label' => 'Type', 'value' => 'Site + média'],
          ['label' => 'Stack', 'value' => 'Laravel'],
        ],
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
