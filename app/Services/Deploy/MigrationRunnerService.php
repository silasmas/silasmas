<?php

namespace App\Services\Deploy;

use Illuminate\Support\Facades\Artisan;

/**
 * Exécute et inspecte les migrations Laravel (production, --force).
 */
class MigrationRunnerService
{
  /**
   * Lance les migrations en attente (équivalent à php artisan migrate --force).
   *
   * @return array{success: bool, exit_code: int, output: string}
   */
  public function run(): array
  {
    $exitCode = Artisan::call('migrate', ['--force' => true]);
    $output = trim(Artisan::output());

    return [
      'success' => $exitCode === 0,
      'exit_code' => $exitCode,
      'output' => $output !== '' ? $output : 'Aucune migration en attente.',
    ];
  }

  /**
   * Retourne l'état des migrations (équivalent à php artisan migrate:status).
   *
   * @return string Sortie console
   */
  public function status(): string
  {
    Artisan::call('migrate:status');
    $output = trim(Artisan::output());

    return $output !== '' ? $output : 'Aucune information de migration disponible.';
  }
}
