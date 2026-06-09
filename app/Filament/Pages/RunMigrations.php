<?php

namespace App\Filament\Pages;

use App\Services\Deploy\MigrationRunnerService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Page admin pour exécuter les migrations en production (--force).
 */
class RunMigrations extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

  protected static ?string $navigationLabel = 'Migrations production';

  protected static ?string $title = 'Migrations production';

  protected static ?string $navigationGroup = 'Système';

  protected static ?int $navigationSort = 99;

  protected static string $view = 'filament.pages.run-migrations';

  public string $statusOutput = '';

  public ?string $lastOutput = null;

  public ?bool $lastSuccess = null;

  /**
   * Charge l'état des migrations au chargement de la page.
   */
  public function mount(MigrationRunnerService $migrationRunner): void
  {
    $this->refreshStatus($migrationRunner);
  }

  /**
   * Bouton d'action dans l'en-tête de la page.
   *
   * @return array<int, Action>
   */
  protected function getHeaderActions(): array
  {
    return [
      Action::make('runMigrations')
        ->label('Exécuter les migrations')
        ->icon('heroicon-o-play')
        ->color('danger')
        ->requiresConfirmation()
        ->modalHeading('Exécuter les migrations en production ?')
        ->modalDescription(
          'Cette action lance php artisan migrate --force. '
          . 'Assurez-vous d\'avoir une sauvegarde de la base avant de continuer.'
        )
        ->modalSubmitActionLabel('Oui, exécuter')
        ->action(fn (MigrationRunnerService $migrationRunner) => $this->executeMigrations($migrationRunner)),
      Action::make('refreshStatus')
        ->label('Actualiser l\'état')
        ->icon('heroicon-o-arrow-path')
        ->color('gray')
        ->action(fn (MigrationRunnerService $migrationRunner) => $this->refreshStatus($migrationRunner)),
    ];
  }

  /**
   * Vérifie que seuls les administrateurs accèdent à cette page.
   */
  public static function canAccess(): bool
  {
    $user = auth()->user();

    if ($user === null) {
      return false;
    }

    return $user->roles()->where('role_name', 'Administrateur')->exists();
  }

  /**
   * Exécute les migrations et affiche le résultat.
   */
  protected function executeMigrations(MigrationRunnerService $migrationRunner): void
  {
    $result = $migrationRunner->run();

    $this->lastOutput = $result['output'];
    $this->lastSuccess = $result['success'];
    $this->refreshStatus($migrationRunner);

    if ($result['success']) {
      Notification::make()
        ->title('Migrations exécutées')
        ->body('Les migrations ont été appliquées avec succès.')
        ->success()
        ->send();

      return;
    }

    Notification::make()
      ->title('Échec des migrations')
      ->body('Consultez la sortie console ci-dessous.')
      ->danger()
      ->send();
  }

  /**
   * Met à jour l'affichage de migrate:status.
   */
  protected function refreshStatus(MigrationRunnerService $migrationRunner): void
  {
    $this->statusOutput = $migrationRunner->status();
  }
}
