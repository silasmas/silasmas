<?php

namespace App\Filament\Resources\TrainingSessionResource\RelationManagers;

use App\Models\Registration;
use App\Services\AcademyPreRegistrationNotifier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Liste les pré-inscrits d'une session depuis la fiche session.
 */
class PreRegistrationsRelationManager extends RelationManager
{
  protected static string $relationship = 'preRegisteredRegistrations';

  protected static ?string $title = 'Pré-inscriptions';

  protected static ?string $modelLabel = 'pré-inscription';

  /**
   * Formulaire (lecture seule pour les champs principaux).
   */
  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Toggle::make('notify_email')
          ->label('Recevoir l\'e-mail d\'ouverture'),
      ]);
  }

  /**
   * Tableau des pré-inscrits liés à la session.
   */
  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('id')
      ->columns([
        Tables\Columns\TextColumn::make('student.full_name')
          ->label('Participant')
          ->searchable(['students.firstname', 'students.lastname']),
        Tables\Columns\TextColumn::make('student.email')
          ->label('E-mail')
          ->copyable(),
        Tables\Columns\TextColumn::make('student.phone')
          ->label('Téléphone'),
        Tables\Columns\TextColumn::make('registered_at')
          ->label('Pré-inscrit le')
          ->dateTime('d/m/Y H:i'),
        Tables\Columns\TextColumn::make('pre_registration_notified_at')
          ->label('Notifié le')
          ->dateTime('d/m/Y H:i')
          ->placeholder('En attente'),
      ])
      ->headerActions([
        Tables\Actions\Action::make('notifyAllPreRegistered')
          ->label('Notifier tous')
          ->icon('heroicon-o-envelope')
          ->color('success')
          ->requiresConfirmation()
          ->visible(fn (): bool => $this->getOwnerRecord()->status === 'open')
          ->action(function (): void {
            $sent = app(AcademyPreRegistrationNotifier::class)
              ->notifySessionPreRegistered($this->getOwnerRecord());

            Notification::make()
              ->title("{$sent} e-mail(s) envoyé(s)")
              ->success()
              ->send();
          }),
      ])
      ->actions([
        Tables\Actions\Action::make('notifyOpen')
          ->label('Notifier')
          ->icon('heroicon-o-envelope')
          ->requiresConfirmation()
          ->action(function (Registration $record): void {
            $sent = app(AcademyPreRegistrationNotifier::class)->sendRegistrationOpen($record, true);

            Notification::make()
              ->title($sent ? 'E-mail envoyé' : 'Envoi impossible')
              ->color($sent ? 'success' : 'danger')
              ->send();
          }),
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->defaultSort('registered_at', 'desc');
  }
}
