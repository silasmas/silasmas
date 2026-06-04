<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Lance les seeders de l'application.
   */
  public function run(): void
  {
    $this->call([
      SdevSeeder::class,
      AcademySeeder::class,
      SiteBlockSeeder::class,
      PortfolioProjectSeeder::class,
      SiteSettingSeeder::class,
    ]);
  }
}
