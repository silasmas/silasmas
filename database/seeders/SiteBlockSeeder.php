<?php

namespace Database\Seeders;

use App\Models\SiteBlock;
use Illuminate\Database\Seeder;

/**
 * Contenu initial du site vitrine (hero, manifeste, témoignages, FAQ, etc.).
 */
class SiteBlockSeeder extends Seeder
{
  /**
   * Crée les blocs de contenu par défaut.
   */
  public function run(): void
  {
    SiteBlock::updateOrCreate(
      ['group' => 'hero', 'title' => 'Construire des produits numériques'],
      [
        'subtitle' => 'Édition 2026 — Kinshasa, RDC',
        'secondary_body' => 'qui comptent',
        'body' => 'Silas Masimango — consultant numérique, fondateur de l\'agence Silas Développe '
          . 'et de la SDev Academy. On accompagne, on construit, on transmet.',
        'sort_order' => 0,
        'is_published' => true,
      ]
    );

    SiteBlock::updateOrCreate(
      ['group' => 'about', 'title' => 'Une vision numérique pour l\'Afrique'],
      [
        'subtitle' => 'À propos',
        'body' => 'SDEV est une société offrant des solutions informatiques, des accompagnements '
          . 'et conseils en stratégie marketing digitale et assure la couverture médiatique '
          . 'des évènements de tout genre.',
        'secondary_body' => 'Avec SDev Academy, nous formons la prochaine génération de talents '
          . 'du numérique en RDC et sur le continent.',
        'image' => null,
        'sort_order' => 0,
        'is_published' => true,
      ]
    );

    $taglines = [
      'Programmation',
      'Développement Web et Mobile',
      'Design',
      'Community Management',
    ];

    foreach ($taglines as $index => $tagline) {
      SiteBlock::updateOrCreate(
        ['group' => 'hero_tagline', 'title' => $tagline],
        ['sort_order' => $index, 'is_published' => true]
      );
    }

    $clientLogos = [
      'PLA & Associés',
      'Action Damien',
      'Fondation JP Tshienda',
      'GAEL',
      'SkillUp',
      'AGR',
      'Ministère & Partenaires',
    ];

    foreach ($clientLogos as $index => $name) {
      SiteBlock::updateOrCreate(
        ['group' => 'client_logo', 'title' => $name],
        ['sort_order' => $index, 'is_published' => true]
      );
    }

    $principles = [
      [
        'title' => 'Penser produit',
        'body' => 'Avant chaque ligne de code, on cadre l\'usage, le marché et la valeur. '
          . 'Pas de prouesse technique sans question business.',
      ],
      [
        'title' => 'Designer la simplicité',
        'body' => 'Un bon produit se reconnaît à ce qu\'il enlève. '
          . 'On défend la clarté contre la complexité.',
      ],
      [
        'title' => 'Livrer, vraiment',
        'body' => 'Mise en ligne, mesure, itération. '
          . 'Un projet n\'existe pas tant qu\'il n\'a pas rencontré ses utilisateurs.',
      ],
      [
        'title' => 'Former la prochaine génération',
        'body' => 'Chaque mission, chaque cours est aussi une occasion de transmettre. '
          . 'C\'est notre manière de faire grandir l\'écosystème.',
      ],
    ];

    foreach ($principles as $index => $principle) {
      SiteBlock::updateOrCreate(
        ['group' => 'principle', 'title' => $principle['title']],
        [
          'body' => $principle['body'],
          'sort_order' => $index,
          'is_published' => true,
        ]
      );
    }

    $testimonials = [
      [
        'author' => 'Dr Jean-Pierre T.',
        'role' => 'Fondation — Directeur',
        'quote' => 'Silas et son équipe ont livré une plateforme solide, élégante et facile à administrer. '
          . 'Un partenaire de confiance.',
      ],
      [
        'author' => 'Pathy L.',
        'role' => 'PLA Cabinet — Associé',
        'quote' => 'Une collaboration fluide du cadrage au lancement. '
          . 'Le site reflète enfin le niveau de notre cabinet.',
      ],
      [
        'author' => 'Grâce N.',
        'role' => 'Développeuse, promotion 2025',
        'quote' => 'La formation SDev Academy est la meilleure que j\'ai suivie à Kinshasa. '
          . 'Concrète, exigeante, et tournée sur de vrais projets.',
      ],
    ];

    foreach ($testimonials as $index => $testimonial) {
      SiteBlock::updateOrCreate(
        ['group' => 'testimonial', 'title' => $testimonial['author']],
        [
          'subtitle' => $testimonial['role'],
          'body' => $testimonial['quote'],
          'sort_order' => $index,
          'is_published' => true,
        ]
      );
    }

    $faqs = [
      [
        'q' => 'Travaillez-vous uniquement en RDC ?',
        'a' => 'Non. Nous accompagnons des clients en RDC, en Afrique centrale et en Europe — '
          . 'la majorité de nos collaborations se font à distance.',
      ],
      [
        'q' => 'Quel est le budget minimum d\'un projet ?',
        'a' => 'Une mission produit complète démarre généralement autour de 5 000 USD. '
          . 'Nous proposons aussi des formats plus courts pour le cadrage, l\'audit ou un MVP.',
      ],
      [
        'q' => 'Quels sont les délais habituels ?',
        'a' => 'Un site sur-mesure : 4 à 8 semaines. Un produit applicatif : 2 à 4 mois. '
          . 'Un cadrage stratégique : 1 à 3 semaines.',
      ],
      [
        'q' => 'Comment se déroulent les cohortes Academy ?',
        'a' => 'Chaque cohorte dure 12 semaines, en hybride. '
          . 'Les sessions live, le mentorat et le projet final sont au cœur de l\'expérience.',
      ],
    ];

    foreach ($faqs as $index => $faq) {
      SiteBlock::updateOrCreate(
        ['group' => 'faq', 'title' => $faq['q']],
        [
          'body' => $faq['a'],
          'sort_order' => $index,
          'is_published' => true,
        ]
      );
    }

    $skills = [
      ['HTML', 90],
      ['CSS', 90],
      ['JavaScript', 75],
      ['PHP', 70],
      ['Bootstrap', 60],
      ['Laravel', 75],
      ['React JS', 65],
      ['React Native', 90],
      ['Photoshop', 85],
    ];

    foreach ($skills as $index => [$name, $level]) {
      SiteBlock::updateOrCreate(
        ['group' => 'skill', 'title' => $name],
        ['level' => $level, 'sort_order' => $index, 'is_published' => true]
      );
    }

    $services = [
      [
        'title' => 'Produits web & mobile',
        'subtitle' => 'Conception et développement d\'applications sur-mesure — du MVP à la mise à l\'échelle.',
        'body' => 'Architecture Next.js, Laravel, React Native. Design system et intégrations API.',
        'icon' => 'globe',
      ],
      [
        'title' => 'Identité & design produit',
        'subtitle' => 'Une direction artistique cohérente entre la marque, l\'interface et le contenu.',
        'body' => 'Identité visuelle, UI/UX premium, prototypes Figma et design system documenté.',
        'icon' => 'design',
      ],
      [
        'title' => 'Marketing & contenu',
        'subtitle' => 'Une présence digitale qui convertit, sans bruit ni recettes datées.',
        'body' => 'Stratégie éditoriale, SEO, lancements produits et campagnes Meta, Google, LinkedIn.',
        'icon' => 'marketing',
      ],
      [
        'title' => 'IA appliquée',
        'subtitle' => 'Intégration d\'IA générative et d\'agents dans vos flux métier — utile, mesurable, sécurisée.',
        'body' => 'Agents internes, RAG, automatisation de contenu, cadrage POC et mise en production.',
        'icon' => 'ia',
      ],
    ];

    foreach ($services as $index => $service) {
      SiteBlock::updateOrCreate(
        ['group' => 'service', 'title' => $service['title']],
        [
          'subtitle' => $service['subtitle'],
          'body' => $service['body'],
          'icon' => $service['icon'],
          'sort_order' => $index,
          'is_published' => true,
        ]
      );
    }

    SiteBlock::updateOrCreate(
      ['group' => 'silas', 'title' => 'Silas Masimango.'],
      [
        'subtitle' => 'Le consultant',
        'secondary_body' => 'Entrepreneur numérique.',
        'body' => 'Je conseille des dirigeants, je construis des produits avec mon agence et je forme '
          . 'la prochaine génération de développeurs avec la SDev Academy. Ma conviction : '
          . 'l\'Afrique a besoin de ses propres bâtisseurs numériques.',
        'sort_order' => 0,
        'is_published' => true,
      ]
    );

    SiteBlock::updateOrCreate(
      ['group' => 'silas_availability', 'title' => 'Q3 — 2026'],
      [
        'body' => 'J\'accepte 4 missions de conseil par trimestre.',
        'sort_order' => 0,
        'is_published' => true,
      ]
    );

    SiteBlock::updateOrCreate(
      ['group' => 'silas_journey_intro', 'title' => 'D\'une chambre de Kinshasa à un écosystème complet.'],
      [
        'body' => 'Quelques étapes qui résument bien ce que nous construisons, année après année.',
        'sort_order' => 0,
        'is_published' => true,
      ]
    );

    $journey = [
      ['year' => '2017', 'title' => 'Premiers projets web', 'body' => 'Premières missions de développement web et mobile en RDC, principalement avec PHP, Laravel et React.'],
      ['year' => '2020', 'title' => 'Création de l\'agence', 'body' => 'Naissance de Silas Développe : un atelier numérique pluridisciplinaire au service des entreprises africaines.'],
      ['year' => '2023', 'title' => 'Premières formations', 'body' => 'Lancement de programmes courts pour former une nouvelle génération de développeurs et de designers.'],
      ['year' => '2025', 'title' => 'SDev Academy', 'body' => 'Ouverture officielle de l\'académie : un programme intensif de 12 semaines centré sur la programmation assistée par l\'IA.'],
      ['year' => '2026', 'title' => 'Agence + Academy + Conseil', 'body' => 'Trois activités, une seule marque : conseil stratégique, agence produit et école — tout au même endroit.'],
    ];

    foreach ($journey as $index => $step) {
      SiteBlock::updateOrCreate(
        ['group' => 'silas_journey', 'title' => $step['title']],
        [
          'subtitle' => $step['year'],
          'body' => $step['body'],
          'sort_order' => $index,
          'is_published' => true,
        ]
      );
    }

    SiteBlock::updateOrCreate(
      ['group' => 'silas_banner', 'title' => 'Penser produit, à l\'échelle du continent.'],
      [
        'subtitle' => 'Conférence — Dakar, 2025',
        'sort_order' => 0,
        'is_published' => true,
      ]
    );

    $offers = [
      ['icon' => 'compass', 'title' => 'Conseil stratégique', 'body' => 'Sessions 1-1 avec des dirigeants pour cadrer un produit, un lancement ou une transformation digitale.'],
      ['icon' => 'lightbulb', 'title' => 'Audit produit & numérique', 'body' => 'Diagnostic court et opérationnel — UX, code, marque, organisation. Un plan de marche actionnable.'],
      ['icon' => 'mic', 'title' => 'Conférences & masterclasses', 'body' => 'Interventions sur l\'IA, la création produit et la nouvelle économie numérique africaine.'],
    ];

    foreach ($offers as $index => $offer) {
      SiteBlock::updateOrCreate(
        ['group' => 'silas_offer', 'title' => $offer['title']],
        [
          'icon' => $offer['icon'],
          'body' => $offer['body'],
          'sort_order' => $index,
          'is_published' => true,
        ]
      );
    }

    SiteBlock::updateOrCreate(
      ['group' => 'silas_cta', 'title' => 'Vous avez une décision à prendre ?'],
      [
        'body' => 'Une session de 60 minutes peut souvent débloquer des semaines d\'hésitation. Parlons-en.',
        'subtitle' => 'Réserver un appel',
        'sort_order' => 0,
        'is_published' => true,
      ]
    );
  }
}
