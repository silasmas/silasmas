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
    $this->call(AcademyEmailTemplateSeeder::class);

    TrainingSession::query()
      ->where('slug', 'programmation-assistee-ia-2026')
      ->update(['status' => 'closed', 'is_featured' => false]);

    TrainingSession::updateOrCreate(
      ['slug' => 'vibe-coding-2026'],
      [
        'title' => 'VIBE CODING : développer avec l\'Intelligence Artificielle',
        'subtitle' => 'La nouvelle façon de développer avec l\'IA — formation en ligne',
        'description' => 'Formation intensive de deux jours pour maîtriser le développement assisté par l\'IA. '
          . 'Projet pratique, outils professionnels et abonnement pro d\'un mois inclus. '
          . 'Paiement via mobile money et carte bancaire.',
        'program' => "Jour 1 — Vibe Coding & outils IA\n"
          . "- Comprendre le développement assisté par l'IA\n"
          . "- Configuration des outils et bonnes pratiques\n"
          . "- Démonstrations et atelier guidé\n\n"
          . "Jour 2 — Projet pratique\n"
          . "- Construction d'un projet concret de A à Z\n"
          . "- Déploiement et retours d'expérience\n"
          . "- Session questions / réponses",
        'start_date' => '2026-06-29',
        'end_date' => '2026-06-30',
        'format' => 'online',
        'status' => 'open',
        'max_participants' => null,
        'is_featured' => true,
        'is_free' => false,
        'price' => 35,
        'currency' => 'USD',
        'cover_image' => 'assets/images/academy/vibe-coding-2026.jpg',
        'registration_benefits' => [
          'Projet pratique guidé de bout en bout',
          'Outils professionnels + abonnement pro 1 mois',
          'Paiement mobile money ou carte bancaire',
        ],
      ]
    );
  }
}
