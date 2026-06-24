<?php

namespace Database\Seeders;

use App\Models\AcademyEmailTemplate;
use Illuminate\Database\Seeder;

/**
 * Modèles d'e-mails Academy par défaut.
 */
class AcademyEmailTemplateSeeder extends Seeder
{
  /**
   * Crée les modèles de base si la table est vide.
   */
  public function run(): void
  {
    if (AcademyEmailTemplate::query()->exists()) {
      return;
    }

    AcademyEmailTemplate::create([
      'name' => 'Relance paiement',
      'slug' => 'relance-paiement',
      'category' => 'payment_reminder',
      'subject' => 'Finalisez votre inscription — {{session_titre}}',
      'description' => 'Rappel pour les participants inscrits dont le paiement n\'est pas terminé.',
      'body' => "Nous avons bien enregistré votre inscription à la formation « {{session_titre}} » ({{session_dates}}).\n\n"
        ."Votre paiement n'est pas encore finalisé (montant : {{montant}} {{devise}}).\n\n"
        ."Pour confirmer votre place, merci de compléter le règlement via le lien ci-dessous :\n{{lien_inscription}}\n\n"
        ."Référence de paiement : {{reference_paiement}}\n\n"
        ."En cas de difficulté, répondez à cet e-mail — nous sommes là pour vous aider.",
      'is_active' => true,
    ]);

    AcademyEmailTemplate::create([
      'name' => 'Message général',
      'slug' => 'message-general',
      'category' => 'general',
      'subject' => 'Message concernant {{session_titre}}',
      'description' => 'Modèle neutre pour communiquer avec les inscrits.',
      'body' => "Bonjour {{nom_complet}},\n\n"
        ."Nous vous contactons au sujet de votre inscription à « {{session_titre}} ».\n\n"
        ."[Votre message ici]\n\n"
        ."Votre espace participant : {{lien_participant}}",
      'is_active' => true,
    ]);
  }
}
