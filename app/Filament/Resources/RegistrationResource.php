<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationResource\Pages;
use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Ressource Filament — inscriptions aux sessions Academy.
 */
class RegistrationResource extends Resource
{
  protected static ?string $model = Registration::class;

  protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

  protected static ?string $navigationGroup = 'SDev Academy';

  protected static ?int $navigationSort = 3;

  protected static ?string $modelLabel = 'Inscription';

  protected static ?string $pluralModelLabel = 'Inscriptions';

  /**
   * Formulaire de création / édition.
   */
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('training_session_id')
          ->label('Session')
          ->relationship('trainingSession', 'title')
          ->searchable()
          ->preload()
          ->required(),
        Forms\Components\Select::make('student_id')
          ->label('Étudiant')
          ->relationship('student', 'email')
          ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name . ' — ' . $record->email)
          ->searchable(['firstname', 'lastname', 'email'])
          ->preload()
          ->required(),
        Forms\Components\Select::make('status')
          ->label('Statut')
          ->options([
            'pending' => 'En attente',
            'pending_payment' => 'En attente de paiement',
            'confirmed' => 'Confirmée',
            'waitlist' => 'Liste d\'attente',
            'cancelled' => 'Annulée',
          ])
          ->required(),
        Forms\Components\TextInput::make('source')
          ->label('Source')
          ->default('admin')
          ->maxLength(255),
        Forms\Components\Textarea::make('motivation')
          ->label('Motivation')
          ->rows(4)
          ->columnSpanFull(),
        Forms\Components\DateTimePicker::make('registered_at')
          ->label('Date d\'inscription')
          ->default(now()),
      ]);
  }

  /**
   * Tableau de liste des inscriptions.
   */
  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('student.full_name')
          ->label('Étudiant')
          ->searchable(['students.firstname', 'students.lastname', 'students.email'])
          ->sortable(),
        Tables\Columns\TextColumn::make('student.email')
          ->label('E-mail')
          ->searchable()
          ->copyable()
          ->toggleable(),
        Tables\Columns\TextColumn::make('trainingSession.title')
          ->label('Session')
          ->limit(35)
          ->sortable(),
        Tables\Columns\TextColumn::make('status')
          ->label('Statut')
          ->badge()
          ->color(fn (string $state): string => match ($state) {
            'confirmed' => 'success',
            'pending' => 'warning',
            'pending_payment' => 'warning',
            'waitlist' => 'info',
            'cancelled' => 'danger',
            default => 'gray',
          })
          ->formatStateUsing(fn (string $state): string => match ($state) {
            'pending' => 'En attente',
            'pending_payment' => 'En attente de paiement',
            'confirmed' => 'Confirmée',
            'waitlist' => 'Liste d\'attente',
            'cancelled' => 'Annulée',
            default => $state,
          }),
        Tables\Columns\TextColumn::make('student.city')
          ->label('Ville')
          ->toggleable(),
        Tables\Columns\TextColumn::make('registered_at')
          ->label('Inscrit le')
          ->dateTime('d/m/Y H:i')
          ->sortable(),
      ])
      ->defaultSort('registered_at', 'desc')
      ->filters([
        Tables\Filters\SelectFilter::make('training_session_id')
          ->label('Session')
          ->relationship('trainingSession', 'title'),
        Tables\Filters\SelectFilter::make('status')
          ->label('Statut')
          ->options([
            'pending' => 'En attente',
            'pending_payment' => 'En attente de paiement',
            'confirmed' => 'Confirmée',
            'waitlist' => 'Liste d\'attente',
            'cancelled' => 'Annulée',
          ]),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
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
      'index' => Pages\ListRegistrations::route('/'),
      'create' => Pages\CreateRegistration::route('/create'),
      'edit' => Pages\EditRegistration::route('/{record}/edit'),
    ];
  }
}
