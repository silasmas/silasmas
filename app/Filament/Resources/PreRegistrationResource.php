<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PreRegistrationResource\Pages;
use App\Models\Registration;
use App\Services\AcademyPreRegistrationNotifier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Ressource Filament — pré-inscriptions Academy (intérêt avant ouverture).
 */
class PreRegistrationResource extends Resource
{
  protected static ?string $model = Registration::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-plus';

  protected static ?string $navigationGroup = 'SDev Academy';

  protected static ?int $navigationSort = 2;

  protected static ?string $modelLabel = 'Pré-inscription';

  protected static ?string $pluralModelLabel = 'Pré-inscriptions';

  protected static ?string $navigationLabel = 'Pré-inscriptions';

  protected static ?string $slug = 'pre-registrations';

  /**
   * Limite la ressource aux pré-inscriptions actives.
   */
  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()->where('status', 'pre_registered');
  }

  /**
   * Badge : pré-inscrits en attente de notification.
   */
  public static function getNavigationBadge(): ?string
  {
    $count = static::getModel()::query()
      ->where('status', 'pre_registered')
      ->whereNull('pre_registration_notified_at')
      ->count();

    return $count > 0 ? (string) $count : null;
  }

  /**
   * Couleur du badge de navigation.
   */
  public static function getNavigationBadgeColor(): ?string
  {
    return 'warning';
  }

  /**
   * Formulaire d'édition d'une pré-inscription.
   */
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('training_session_id')
          ->label('Session')
          ->relationship('trainingSession', 'title')
          ->disabled()
          ->dehydrated(false),
        Forms\Components\Placeholder::make('student_label')
          ->label('Participant')
          ->content(fn (?Registration $record): string => $record?->student
            ? $record->student->full_name.' — '.$record->student->email
            : '—'),
        Forms\Components\Toggle::make('notify_email')
          ->label('Recevoir l\'e-mail d\'ouverture')
          ->helperText('Désactivez pour exclure cette personne de l\'envoi automatique.'),
        Forms\Components\DateTimePicker::make('registered_at')
          ->label('Pré-inscrit le')
          ->disabled()
          ->dehydrated(false),
        Forms\Components\DateTimePicker::make('pre_registration_notified_at')
          ->label('E-mail d\'ouverture envoyé le')
          ->disabled()
          ->dehydrated(false),
      ]);
  }

  /**
   * Tableau de liste des pré-inscriptions.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('student.full_name')
          ->label('Participant')
          ->searchable(['students.firstname', 'students.lastname', 'students.email'])
          ->sortable(),
        Tables\Columns\TextColumn::make('student.email')
          ->label('E-mail')
          ->searchable()
          ->copyable(),
        Tables\Columns\TextColumn::make('student.phone')
          ->label('Téléphone')
          ->toggleable(),
        Tables\Columns\TextColumn::make('trainingSession.title')
          ->label('Session')
          ->limit(40)
          ->sortable(),
        Tables\Columns\TextColumn::make('registered_at')
          ->label('Pré-inscrit le')
          ->dateTime('d/m/Y H:i')
          ->sortable(),
        Tables\Columns\IconColumn::make('notify_email')
          ->label('E-mail')
          ->boolean()
          ->toggleable(),
        Tables\Columns\TextColumn::make('pre_registration_notified_at')
          ->label('Notifié le')
          ->dateTime('d/m/Y H:i')
          ->placeholder('En attente')
          ->sortable(),
      ])
      ->defaultSort('registered_at', 'desc')
      ->filters([
        Tables\Filters\SelectFilter::make('training_session_id')
          ->label('Session')
          ->relationship('trainingSession', 'title'),
        Tables\Filters\TernaryFilter::make('pre_registration_notified_at')
          ->label('E-mail d\'ouverture')
          ->nullable()
          ->trueLabel('Envoyé')
          ->falseLabel('En attente')
          ->queries(
            true: fn (Builder $query) => $query->whereNotNull('pre_registration_notified_at'),
            false: fn (Builder $query) => $query->whereNull('pre_registration_notified_at'),
          ),
      ])
      ->actions([
        Tables\Actions\Action::make('notifyOpen')
          ->label('Envoyer l\'ouverture')
          ->icon('heroicon-o-envelope')
          ->color('success')
          ->requiresConfirmation()
          ->visible(fn (Registration $record): bool => (bool) ($record->notify_email ?? true))
          ->action(function (Registration $record): void {
            $sent = app(AcademyPreRegistrationNotifier::class)->sendRegistrationOpen($record, true);

            if ($sent) {
              Notification::make()
                ->title('E-mail envoyé')
                ->success()
                ->send();

              return;
            }

            Notification::make()
              ->title('Envoi impossible')
              ->body('Vérifiez que la session est ouverte aux inscriptions et que l\'e-mail est valide.')
              ->danger()
              ->send();
          }),
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\BulkAction::make('notifyOpenBulk')
            ->label('Envoyer l\'ouverture')
            ->icon('heroicon-o-envelope')
            ->requiresConfirmation()
            ->action(function ($records): void {
              $notifier = app(AcademyPreRegistrationNotifier::class);
              $sent = 0;

              foreach ($records as $record) {
                if ($notifier->sendRegistrationOpen($record, true)) {
                  $sent++;
                }
              }

              Notification::make()
                ->title("{$sent} e-mail(s) envoyé(s)")
                ->success()
                ->send();
            }),
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  /**
   * @return array<class-string>
   */
  public static function getPages(): array
  {
    return [
      'index' => Pages\ListPreRegistrations::route('/'),
      'edit' => Pages\EditPreRegistration::route('/{record}/edit'),
    ];
  }

  /**
   * Pas de création manuelle depuis cette ressource.
   */
  public static function canCreate(): bool
  {
    return false;
  }
}
