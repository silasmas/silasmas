<?php

namespace App\Filament\Resources\TrainingSessionResource\Pages;

use App\Filament\Resources\TrainingSessionResource;
use App\Services\AcademyPreRegistrationNotifier;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTrainingSession extends EditRecord
{
  protected static string $resource = TrainingSessionResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('notifyPreRegistered')
        ->label('Notifier les pré-inscrits')
        ->icon('heroicon-o-envelope')
        ->color('success')
        ->requiresConfirmation()
        ->modalDescription('Envoie l\'e-mail d\'ouverture des inscriptions à tous les pré-inscrits de cette session qui ne l\'ont pas encore reçu.')
        ->visible(fn (): bool => $this->record->status === 'open'
          && $this->record->preRegisteredRegistrations()->whereNull('pre_registration_notified_at')->exists())
        ->action(function (): void {
          $sent = app(AcademyPreRegistrationNotifier::class)
            ->notifySessionPreRegistered($this->record);

          Notification::make()
            ->title("{$sent} e-mail(s) envoyé(s)")
            ->success()
            ->send();
        }),
      Actions\DeleteAction::make(),
    ];
  }
}
