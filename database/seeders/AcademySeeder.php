<?php

namespace Database\Seeders;

use App\Models\TrainingSession;
use Illuminate\Database\Seeder;

/**
 * Données initiales SDev Academy (première session juin 2026).
 */
class AcademySeeder extends Seeder
{
  /**
   * Crée la session de lancement SDev Academy.
   */
  public function run(): void
  {
    TrainingSession::updateOrCreate(
      ['slug' => 'programmation-assistee-ia-2026'],
      [
        'title' => 'Programmation assistée par l\'Intelligence Artificielle',
        'subtitle' => 'Première édition SDev Academy — en ligne',
        'description' => 'Formation introductive sur la programmation assistée par l\'IA. '
          . 'Découvrez comment intégrer l\'intelligence artificielle dans votre workflow de développement '
          . 'et accélérer la création de solutions numériques.',
        'program' => "Jour 1 — Fondamentaux\n"
          . "- Panorama de l'IA appliquée au développement\n"
          . "- Outils et bonnes pratiques\n"
          . "- Démonstrations pratiques\n\n"
          . "Jour 2 — Mise en pratique\n"
          . "- Ateliers guidés\n"
          . "- Projets concrets\n"
          . "- Questions / réponses",
        'start_date' => '2026-06-29',
        'end_date' => '2026-06-30',
        'format' => 'online',
        'status' => 'open',
        'max_participants' => null,
        'is_featured' => true,
      ]
    );
  }
}
