<?php

namespace Database\Seeders;

use App\Models\SiteBlock;
use Illuminate\Database\Seeder;

/**
 * Contenu initial du site vitrine (à propos, compétences, services).
 */
class SiteBlockSeeder extends Seeder
{
  /**
   * Crée les blocs de contenu par défaut.
   */
  public function run(): void
  {
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
        'title' => 'Création site web',
        'body' => 'Nous créons et hébergeons des sites web pour les entreprises et les personnes.',
        'icon' => 'globe',
      ],
      [
        'title' => 'Création applis',
        'body' => 'Nous créons des applis mobiles Android et iOS pour les entreprises et les personnes.',
        'icon' => 'mobile',
      ],
      [
        'title' => 'Marketing',
        'body' => 'Nous facilitons la visibilité de vos produits chez vos clients physiquement et en ligne.',
        'icon' => 'marketing',
      ],
      [
        'title' => 'Design & Montage',
        'body' => 'Nous concevons des affiches, des tracts, des invitations, des logos et autres.',
        'icon' => 'design',
      ],
    ];

    foreach ($services as $index => $service) {
      SiteBlock::updateOrCreate(
        ['group' => 'service', 'title' => $service['title']],
        [
          'body' => $service['body'],
          'icon' => $service['icon'],
          'sort_order' => $index,
          'is_published' => true,
        ]
      );
    }
  }
}
